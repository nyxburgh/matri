<?php
// app/modules/Profile/Controllers/ProfileController.php

class ProfileController extends Controller {

    private int $maxPhotos = 10;
    private int $maxPhotoMb = 5;

    // ── My Profile ────────────────────────────────────────────────────────────

    public function myProfile(array $params = []): void {
        $this->requireUser();
        $userId = Session::get('user_id');
        $this->loadAndShowProfile($userId, true);
    }

    // ── View Public Profile ───────────────────────────────────────────────────

    public function view(array $params = []): void {
        $this->requireUser();
        $profileId = $params['profile_id'] ?? '';
        $viewerId  = Session::get('user_id');

        $user = Database::fetchOne(
            "SELECT u.*, p.* FROM users u JOIN profiles p ON p.user_id = u.id
             WHERE u.profile_id = ? AND u.status = 'active' AND p.admin_approved = 'approved' LIMIT 1",
            [$profileId]
        );

        if (!$user) {
            http_response_code(404);
            $this->view('User/Views/errors/404.php', ['pageTitle' => 'Profile Not Found'], 'User/Views/layouts/main.php');
            return;
        }

        $targetId = $user['user_id'] ?? $user['id'];

        // Log profile view (throttle: once per viewer per profile per day)
        if ($targetId !== $viewerId) {
            $already = Database::fetchOne(
                "SELECT id FROM profile_views WHERE viewer_id = ? AND viewed_id = ? AND DATE(viewed_at) = CURDATE()",
                [$viewerId, $targetId]
            );
            if (!$already) {
                Database::execute(
                    "INSERT INTO profile_views (viewer_id, viewed_id) VALUES (?, ?)",
                    [$viewerId, $targetId]
                );
                // Notify the profile owner
                Database::execute(
                    "INSERT INTO notifications (user_id, type, ref_id, title, body, channel)
                     VALUES (?, 'profile_viewed', ?, ?, ?, 'inapp')",
                    [$targetId, $viewerId,
                     Session::get('user_name') . ' viewed your profile',
                     'Someone viewed your profile']
                );
            }
        }

        $this->loadAndShowProfile($targetId, $targetId === $viewerId, $viewerId);
    }

    private function loadAndShowProfile(int $userId, bool $isOwner = false, ?int $viewerId = null): void {
        $viewerId = $viewerId ?? Session::get('user_id');

        $user = Database::fetchOne(
            "SELECT u.id, u.profile_id, u.name, u.gender, u.account_for,
                    u.email_verified, u.mobile_verified, u.last_active, u.created_at,
                    p.*
             FROM users u LEFT JOIN profiles p ON p.user_id = u.id
             WHERE u.id = ? LIMIT 1",
            [$userId]
        );
        if (!$user) { http_response_code(404); die('Profile not found.'); }

        $photos      = Database::fetchAll(
            "SELECT * FROM photos WHERE user_id = ? AND is_approved = 'approved' ORDER BY is_primary DESC, sort_order ASC",
            [$userId]
        );
        $family      = Database::fetchOne("SELECT * FROM family_details WHERE user_id = ? LIMIT 1", [$userId]);
        $horoscope   = Database::fetchOne("SELECT * FROM horoscopes WHERE user_id = ? LIMIT 1", [$userId]);
        $subscription = $this->getUserPlan($userId);
        $isShortlisted = false;

        // Interest status between viewer and this profile
        $interestStatus = null;
        if ($viewerId && !$isOwner) {
            $interest = Database::fetchOne(
                "SELECT * FROM interests WHERE (sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?) LIMIT 1",
                [$viewerId, $userId, $userId, $viewerId]
            );
            $interestStatus = $interest ? $interest['status'] : null;
            $isShortlisted  = (bool)Database::fetchOne(
                "SELECT id FROM shortlists WHERE user_id = ? AND shortlisted_id = ? LIMIT 1",
                [$viewerId, $userId]
            );
        }

        $pageTitle = $user['name'] . ' — Profile';
        $this->view(
            'User/Views/profile/view.php',
            compact('user','photos','family','horoscope','subscription',
                    'isOwner','interestStatus','isShortlisted','pageTitle'),
            'User/Views/layouts/main.php'
        );
    }

    // ── Profile Creation Wizard ────────────────────────────────────────────────

    public function createStep1(array $params = []): void {
        $this->requireUser();
        $userId  = Session::get('user_id');
        $profile = Database::fetchOne("SELECT * FROM profiles WHERE user_id = ? LIMIT 1", [$userId]);
        $error   = Session::getFlash('error');
        $pageTitle = 'Create Profile — Basic Info';
        $this->view('User/Views/profile/create/step1_basic.php', compact('profile','error','pageTitle'), 'User/Views/layouts/main.php');
    }

    public function saveBasic(array $params = []): void {
        $this->requireUser();
        $this->verifyCsrf();
        $userId = Session::get('user_id');

        $fields = [
            'dob'             => $_POST['dob'] ?? null,
            'height_cm'       => (int)($_POST['height_cm'] ?? 0) ?: null,
            'weight_kg'       => (int)($_POST['weight_kg'] ?? 0) ?: null,
            'complexion'      => $this->input('complexion'),
            'body_type'       => $this->input('body_type'),
            'marital_status'  => $this->input('marital_status', 'never_married'),
            'mother_tongue'   => $this->input('mother_tongue'),
            'country'         => $this->input('country', 'India'),
            'state'           => $this->input('state'),
            'city'            => $this->input('city'),
            'pincode'         => $this->input('pincode'),
            'religion'        => $this->input('religion'),
            'caste'           => $this->input('caste'),
            'sub_caste'       => $this->input('sub_caste'),
            'gothram'         => $this->input('gothram'),
            'star'            => $this->input('star'),
            'raasi'           => $this->input('raasi'),
            'dhosam'          => $this->input('dhosam', 'none'),
            'education'       => $this->input('education'),
            'education_detail'=> $this->input('education_detail'),
            'employed_in'     => $this->input('employed_in'),
            'occupation'      => $this->input('occupation'),
            'annual_income'   => $this->input('annual_income'),
            'diet'            => $this->input('diet'),
            'smoking'         => $this->input('smoking', 'no'),
            'drinking'        => $this->input('drinking', 'no'),
            'about_me'        => $this->sanitize(substr($_POST['about_me'] ?? '', 0, 1000)),
        ];

        // Calculate age from DOB
        if (!empty($fields['dob'])) {
            $fields['age'] = (int)((new DateTime())->diff(new DateTime($fields['dob']))->y);
        }

        $existing = Database::fetchOne("SELECT id FROM profiles WHERE user_id = ? LIMIT 1", [$userId]);
        if ($existing) {
            $sets  = implode(', ', array_map(fn($k) => "$k = ?", array_keys($fields)));
            $vals  = array_values($fields);
            $vals[] = $userId;
            Database::execute("UPDATE profiles SET $sets, updated_at = NOW() WHERE user_id = ?", $vals);
        } else {
            $fields['user_id'] = $userId;
            $cols = implode(', ', array_keys($fields));
            $phs  = implode(', ', array_fill(0, count($fields), '?'));
            Database::execute("INSERT INTO profiles ($cols) VALUES ($phs)", array_values($fields));
        }

        // Generate SEO slug
        $name = Session::get('user_name');
        $slug = strtolower(preg_replace('/[^a-z0-9]+/', '-', $name)) . '-' . strtolower(Session::get('user_profile_id'));
        Database::execute("UPDATE profiles SET slug = ? WHERE user_id = ?", [$slug, $userId]);

        $this->redirect('profile/create/family');
    }

    public function createStep2(array $params = []): void {
        $this->requireUser();
        $userId  = Session::get('user_id');
        $family  = Database::fetchOne("SELECT * FROM family_details WHERE user_id = ? LIMIT 1", [$userId]);
        $error   = Session::getFlash('error');
        $pageTitle = 'Create Profile — Family Details';
        $this->view('User/Views/profile/create/step2_family.php', compact('family','error','pageTitle'), 'User/Views/layouts/main.php');
    }

    public function saveFamily(array $params = []): void {
        $this->requireUser();
        $this->verifyCsrf();
        $userId = Session::get('user_id');

        $fields = [
            'father_name'     => $this->input('father_name'),
            'father_status'   => $this->input('father_status'),
            'mother_name'     => $this->input('mother_name'),
            'mother_status'   => $this->input('mother_status'),
            'brothers'        => (int)($_POST['brothers'] ?? 0),
            'brothers_married'=> (int)($_POST['brothers_married'] ?? 0),
            'sisters'         => (int)($_POST['sisters'] ?? 0),
            'sisters_married' => (int)($_POST['sisters_married'] ?? 0),
            'family_type'     => $this->input('family_type'),
            'family_status'   => $this->input('family_status'),
            'family_values'   => $this->input('family_values'),
            'native_place'    => $this->input('native_place'),
        ];

        $exists = Database::fetchOne("SELECT id FROM family_details WHERE user_id = ? LIMIT 1", [$userId]);
        if ($exists) {
            $sets = implode(', ', array_map(fn($k) => "$k = ?", array_keys($fields)));
            $vals = array_values($fields);
            $vals[] = $userId;
            Database::execute("UPDATE family_details SET $sets, updated_at = NOW() WHERE user_id = ?", $vals);
        } else {
            $fields['user_id'] = $userId;
            $cols = implode(', ', array_keys($fields));
            $phs  = implode(', ', array_fill(0, count($fields), '?'));
            Database::execute("INSERT INTO family_details ($cols) VALUES ($phs)", array_values($fields));
        }

        $this->redirect('profile/create/horoscope');
    }

    public function createStep3(array $params = []): void {
        $this->requireUser();
        $userId    = Session::get('user_id');
        $horoscope = Database::fetchOne("SELECT * FROM horoscopes WHERE user_id = ? LIMIT 1", [$userId]);
        $error     = Session::getFlash('error');
        $pageTitle = 'Create Profile — Horoscope';
        $this->view('User/Views/profile/create/step3_horoscope.php', compact('horoscope','error','pageTitle'), 'User/Views/layouts/main.php');
    }

    public function saveHoroscope(array $params = []): void {
        $this->requireUser();
        $this->verifyCsrf();
        $userId = Session::get('user_id');

        // Planets list for both charts
        $planets = ['Sun','Moon','Mars','Mercury','Jupiter','Venus','Saturn','Rahu','Ketu','Lagna'];

        // Build Rasi chart JSON: house => [planets]
        $rasiChart = [];
        for ($h = 1; $h <= 12; $h++) {
            $rasiChart[$h] = $_POST['rasi'][$h] ?? [];
        }

        // Build Navamsa chart JSON
        $navamsaChart = [];
        for ($h = 1; $h <= 12; $h++) {
            $navamsaChart[$h] = $_POST['navamsa'][$h] ?? [];
        }

        $fields = [
            'birth_date'    => !empty($_POST['birth_date'])  ? $_POST['birth_date']  : null,
            'birth_time'    => !empty($_POST['birth_time'])  ? $_POST['birth_time']  : null,
            'birth_place'   => $this->input('birth_place'),
            'rasi_chart'    => json_encode($rasiChart),
            'navamsa_chart' => json_encode($navamsaChart),
            'lagna'         => $this->input('lagna'),
            'rasi'          => $this->input('rasi'),
            'rasi_lord'     => $this->input('rasi_lord'),
            'nakshatra'     => $this->input('nakshatra'),
            'nakshatra_pada'=> (int)($_POST['nakshatra_pada'] ?? 0) ?: null,
            'sun_rasi'      => $this->input('sun_rasi'),
            'moon_rasi'     => $this->input('moon_rasi'),
            'mars_rasi'     => $this->input('mars_rasi'),
            'mercury_rasi'  => $this->input('mercury_rasi'),
            'jupiter_rasi'  => $this->input('jupiter_rasi'),
            'venus_rasi'    => $this->input('venus_rasi'),
            'saturn_rasi'   => $this->input('saturn_rasi'),
            'rahu_rasi'     => $this->input('rahu_rasi'),
            'ketu_rasi'     => $this->input('ketu_rasi'),
            'chevvai_dhosam'=> (int)($_POST['chevvai_dhosam'] ?? 0),
            'rahu_dhosam'   => (int)($_POST['rahu_dhosam'] ?? 0),
            'kala_sarpa'    => (int)($_POST['kala_sarpa'] ?? 0),
            'visibility'    => $this->input('visibility', 'members'),
        ];

        // Sync dosham flags to profile table
        Database::execute(
            "UPDATE profiles SET dhosam = ? WHERE user_id = ?",
            [$fields['chevvai_dhosam'] ? 'chevvai' : ($fields['rahu_dhosam'] ? 'rahu' : 'none'), $userId]
        );

        $exists = Database::fetchOne("SELECT id FROM horoscopes WHERE user_id = ? LIMIT 1", [$userId]);
        if ($exists) {
            $sets = implode(', ', array_map(fn($k) => "$k = ?", array_keys($fields)));
            $vals = array_values($fields);
            $vals[] = $userId;
            Database::execute("UPDATE horoscopes SET $sets, updated_at = NOW() WHERE user_id = ?", $vals);
        } else {
            $fields['user_id'] = $userId;
            $cols = implode(', ', array_keys($fields));
            $phs  = implode(', ', array_fill(0, count($fields), '?'));
            Database::execute("INSERT INTO horoscopes ($cols) VALUES ($phs)", array_values($fields));
        }

        $this->redirect('profile/create/photos');
    }

    public function createStep4(array $params = []): void {
        $this->requireUser();
        $userId    = Session::get('user_id');
        $photos    = Database::fetchAll("SELECT * FROM photos WHERE user_id = ? ORDER BY is_primary DESC, sort_order", [$userId]);
        $pageTitle = 'Create Profile — Photos';
        $maxPhotos = $this->maxPhotos;
        $this->view('User/Views/profile/create/step4_photos.php', compact('photos','pageTitle','maxPhotos'), 'User/Views/layouts/main.php');
    }

    // ── Photo Management ──────────────────────────────────────────────────────

    public function uploadPhoto(array $params = []): void {
        $this->requireUser();
        $this->verifyCsrf();
        $userId = Session::get('user_id');

        $count = Database::fetchOne("SELECT COUNT(*) c FROM photos WHERE user_id = ?", [$userId])['c'] ?? 0;
        if ($count >= $this->maxPhotos) {
            $this->json(['success' => false, 'message' => "Maximum {$this->maxPhotos} photos allowed."]);
        }

        $file = $_FILES['photo'] ?? null;
        if (!$file || $file['error'] !== UPLOAD_ERR_OK) {
            $this->json(['success' => false, 'message' => 'Upload failed. Please try again.']);
        }

        $allowedMime = ['image/jpeg', 'image/png', 'image/webp'];
        $mime        = mime_content_type($file['tmp_name']);
        if (!in_array($mime, $allowedMime)) {
            $this->json(['success' => false, 'message' => 'Only JPEG, PNG, and WEBP images are allowed.']);
        }
        if ($file['size'] > $this->maxPhotoMb * 1024 * 1024) {
            $this->json(['success' => false, 'message' => "File size must be under {$this->maxPhotoMb}MB."]);
        }

        $ext      = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'][$mime];
        $folder   = UPLOAD_PATH . '/' . $userId;
        if (!is_dir($folder)) mkdir($folder, 0755, true);

        $fileName = uniqid('photo_', true) . '.' . $ext;
        $fullPath = $folder . '/' . $fileName;
        $relPath  = 'photos/' . $userId . '/' . $fileName;

        if (!move_uploaded_file($file['tmp_name'], $fullPath)) {
            $this->json(['success' => false, 'message' => 'Failed to save image.']);
        }

        $isPrimary = ($count === 0) ? 1 : 0;
        $photoId   = Database::insert(
            "INSERT INTO photos (user_id, file_path, file_name, mime_type, file_size, is_primary, sort_order)
             VALUES (?, ?, ?, ?, ?, ?, ?)",
            [$userId, $relPath, $fileName, $mime, $file['size'], $isPrimary, (int)$count]
        );

        $this->logActivity('upload_photo', 'Profile', (int)$photoId);
        $this->json(['success' => true, 'photo_id' => $photoId, 'path' => APP_URL . '/storage/' . $relPath]);
    }

    public function deletePhoto(array $params = []): void {
        $this->requireUser();
        $this->verifyCsrf();
        $userId  = Session::get('user_id');
        $photoId = (int)($_POST['photo_id'] ?? 0);

        $photo = Database::fetchOne("SELECT * FROM photos WHERE id = ? AND user_id = ? LIMIT 1", [$photoId, $userId]);
        if (!$photo) { $this->json(['success' => false, 'message' => 'Photo not found.']); }

        $fullPath = STORAGE_PATH . '/' . $photo['file_path'];
        if (file_exists($fullPath)) unlink($fullPath);

        Database::execute("DELETE FROM photos WHERE id = ?", [$photoId]);

        // If deleted was primary, set next one as primary
        if ($photo['is_primary']) {
            $next = Database::fetchOne("SELECT id FROM photos WHERE user_id = ? ORDER BY sort_order LIMIT 1", [$userId]);
            if ($next) Database::execute("UPDATE photos SET is_primary = 1 WHERE id = ?", [$next['id']]);
        }

        $this->json(['success' => true]);
    }

    public function setPrimary(array $params = []): void {
        $this->requireUser();
        $this->verifyCsrf();
        $userId  = Session::get('user_id');
        $photoId = (int)($_POST['photo_id'] ?? 0);

        Database::execute("UPDATE photos SET is_primary = 0 WHERE user_id = ?", [$userId]);
        Database::execute("UPDATE photos SET is_primary = 1 WHERE id = ? AND user_id = ?", [$photoId, $userId]);
        $this->json(['success' => true]);
    }

    // ── Edit Profile ──────────────────────────────────────────────────────────

    public function edit(array $params = []): void {
        $this->requireUser();
        $userId    = Session::get('user_id');
        $profile   = Database::fetchOne("SELECT * FROM profiles WHERE user_id = ? LIMIT 1", [$userId]);
        $photos    = Database::fetchAll("SELECT * FROM photos WHERE user_id = ? ORDER BY is_primary DESC, sort_order", [$userId]);
        $family    = Database::fetchOne("SELECT * FROM family_details WHERE user_id = ? LIMIT 1", [$userId]);
        $horoscope = Database::fetchOne("SELECT * FROM horoscopes WHERE user_id = ? LIMIT 1", [$userId]);
        $error     = Session::getFlash('error');
        $flash     = Session::getFlash('success');
        $pageTitle = 'Edit My Profile';

        $this->view(
            'User/Views/profile/edit.php',
            compact('profile','photos','family','horoscope','error','flash','pageTitle'),
            'User/Views/layouts/main.php'
        );
    }

    public function editBasic(array $params = []): void {
        // Reuse saveBasic logic, then redirect to edit page
        $this->saveBasic($params);
        // Override redirect
        Session::flash('success', 'Profile updated successfully.');
        $this->redirect('profile/edit');
    }

    public function savePartnerPref(array $params = []): void {
        $this->requireUser();
        $this->verifyCsrf();
        $userId = Session::get('user_id');

        $pref = [
            'age_from'       => (int)($_POST['age_from'] ?? 18),
            'age_to'         => (int)($_POST['age_to'] ?? 50),
            'height_from'    => (int)($_POST['height_from'] ?? 0) ?: null,
            'height_to'      => (int)($_POST['height_to'] ?? 0) ?: null,
            'marital_status' => $_POST['marital_status'] ?? [],
            'religion'       => $_POST['religion'] ?? [],
            'caste'          => $_POST['caste'] ?? [],
            'education'      => $_POST['education'] ?? [],
            'employed_in'    => $_POST['employed_in'] ?? [],
            'annual_income'  => $this->input('annual_income'),
            'diet'           => $_POST['diet'] ?? [],
            'locations'      => $_POST['locations'] ?? [],
            'about_partner'  => $this->sanitize(substr($_POST['about_partner'] ?? '', 0, 500)),
        ];

        Database::execute(
            "UPDATE profiles SET partner_pref = ?, updated_at = NOW() WHERE user_id = ?",
            [json_encode($pref), $userId]
        );

        Session::flash('success', 'Partner preferences saved.');
        $this->redirect('profile/edit');
    }

    public function savePrivacy(array $params = []): void {
        $this->requireUser();
        $this->verifyCsrf();
        $userId = Session::get('user_id');

        $profileVisible = $this->input('profile_visible', 'all');
        $showContact    = (int)($_POST['show_contact'] ?? 0);

        Database::execute(
            "UPDATE profiles SET profile_visible = ?, show_contact = ?, updated_at = NOW() WHERE user_id = ?",
            [$profileVisible, $showContact, $userId]
        );

        // Update horoscope visibility
        $horoVisibility = $this->input('horoscope_visibility', 'members');
        Database::execute("UPDATE horoscopes SET visibility = ? WHERE user_id = ?", [$horoVisibility, $userId]);

        $this->logActivity('update_privacy', 'Profile');
        Session::flash('success', 'Privacy settings saved.');
        $this->redirect('profile/edit');
    }

    // ── Mark Profile Complete ──────────────────────────────────────────────────

    private function markProfileComplete(int $userId): void {
        $profile = Database::fetchOne("SELECT * FROM profiles WHERE user_id = ? LIMIT 1", [$userId]);
        $required = ['dob', 'religion', 'education', 'occupation', 'city'];
        $complete = true;
        foreach ($required as $f) {
            if (empty($profile[$f])) { $complete = false; break; }
        }
        if ($complete) {
            Database::execute("UPDATE users SET profile_complete = 1 WHERE id = ?", [$userId]);
            Session::set('user_profile_complete', true);
        }
    }

    // ── Shortlist ─────────────────────────────────────────────────────────────

    public function toggleShortlist(array $params = []): void {
        $this->requireUser();
        $this->verifyCsrf();
        $userId      = Session::get('user_id');
        $targetUserId= (int)($_POST['target_id'] ?? 0);

        $exists = Database::fetchOne(
            "SELECT id FROM shortlists WHERE user_id = ? AND shortlisted_id = ? LIMIT 1",
            [$userId, $targetUserId]
        );

        if ($exists) {
            Database::execute("DELETE FROM shortlists WHERE user_id = ? AND shortlisted_id = ?", [$userId, $targetUserId]);
            $this->json(['success' => true, 'action' => 'removed']);
        } else {
            Database::execute(
                "INSERT INTO shortlists (user_id, shortlisted_id) VALUES (?, ?)",
                [$userId, $targetUserId]
            );
            $this->json(['success' => true, 'action' => 'added']);
        }
    }

    public function shortlistIndex(array $params = []): void {
        $this->requireUser();
        $userId = Session::get('user_id');
        $page   = max(1, (int)($_GET['page'] ?? 1));

        $sql = "SELECT sl.created_at AS shortlisted_at, u.id, u.profile_id, u.name, u.gender,
                       p.age, p.city, p.religion, p.education, p.occupation, p.marital_status,
                       (SELECT ph.file_path FROM photos ph WHERE ph.user_id=u.id AND ph.is_primary=1
                        AND ph.is_approved='approved' LIMIT 1) AS photo
                FROM shortlists sl
                JOIN users u ON u.id = sl.shortlisted_id
                LEFT JOIN profiles p ON p.user_id = u.id
                WHERE sl.user_id = ? AND u.status = 'active'
                ORDER BY sl.created_at DESC";

        $result    = Database::paginate($sql, [$userId], $page, 12);
        $pageTitle = 'My Shortlist';

        $this->view(
            'User/Views/profile/shortlist.php',
            array_merge($result, compact('pageTitle')),
            'User/Views/layouts/main.php'
        );
    }

    // ── Report ────────────────────────────────────────────────────────────────

    public function report(array $params = []): void {
        $this->requireUser();
        $this->verifyCsrf();
        $reporterId  = Session::get('user_id');
        $reportedId  = (int)($_POST['reported_id'] ?? 0);
        $reason      = $this->input('reason');
        $description = $this->sanitize(substr($_POST['description'] ?? '', 0, 500));

        if (!$reportedId || $reportedId === $reporterId) {
            $this->json(['success' => false, 'message' => 'Invalid request.']);
        }

        // Check for duplicate
        $dup = Database::fetchOne(
            "SELECT id FROM reports WHERE reporter_id = ? AND reported_id = ? LIMIT 1",
            [$reporterId, $reportedId]
        );
        if ($dup) {
            $this->json(['success' => false, 'message' => 'You have already reported this profile.']);
        }

        Database::execute(
            "INSERT INTO reports (reporter_id, reported_id, reason, description) VALUES (?, ?, ?, ?)",
            [$reporterId, $reportedId, $reason, $description]
        );
        $this->json(['success' => true, 'message' => 'Report submitted. Our team will review it.']);
    }
}
