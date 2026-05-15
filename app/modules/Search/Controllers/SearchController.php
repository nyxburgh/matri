<?php
// app/modules/Search/Controllers/SearchController.php

class SearchController extends Controller {

    private int $perPage = 12;

    public function index(array $params = []): void {
        $this->requireUser();
        $pageTitle = 'Search Profiles';
        $this->view('Search/Views/index.php', compact('pageTitle'), 'User/Views/layouts/main.php');
    }

    public function results(array $params = []): void {
        $this->requireUser();
        $userId    = Session::get('user_id');
        $gender    = Session::get('user_gender');
        $page      = max(1, (int)($_GET['page'] ?? 1));
        $plan      = $this->getUserPlan($userId);
        $isAdvanced= (bool)($plan['advanced_search'] ?? 0);

        $oppGender = $gender === 'male' ? 'female' : 'male';
        $searchGender = $this->input('gender', $oppGender);

        $where  = ["u.status = 'active'", "p.admin_approved = 'approved'", "u.id != ?"];
        $binds  = [$userId];

        // Basic filters (all plans)
        if ($ageFrom = (int)($_GET['age_from'] ?? 0)) { $where[] = "p.age >= ?"; $binds[] = $ageFrom; }
        if ($ageTo   = (int)($_GET['age_to']   ?? 0)) { $where[] = "p.age <= ?"; $binds[] = $ageTo; }
        if ($searchGender) { $where[] = "u.gender = ?"; $binds[] = $searchGender; }
        if ($religion = $this->input('religion'))    { $where[] = "p.religion LIKE ?"; $binds[] = "%$religion%"; }
        if ($city     = $this->input('city'))        { $where[] = "(p.city LIKE ? OR p.state LIKE ?)"; $binds[] = "%$city%"; $binds[] = "%$city%"; }
        if ($marital  = $this->input('marital_status')) { $where[] = "p.marital_status = ?"; $binds[] = $marital; }
        if ($pid      = $this->input('profile_id'))  { $where[] = "u.profile_id = ?"; $binds[] = strtoupper($pid); }

        // Advanced filters (Silver+ plans only)
        if ($isAdvanced) {
            if ($caste    = $this->input('caste'))    { $where[] = "p.caste LIKE ?"; $binds[] = "%$caste%"; }
            if ($edu      = $this->input('education'))  { $where[] = "p.education LIKE ?"; $binds[] = "%$edu%"; }
            if ($employed = $this->input('employed_in')){ $where[] = "p.employed_in = ?"; $binds[] = $employed; }
            if ($income   = $this->input('annual_income')){ $where[] = "p.annual_income = ?"; $binds[] = $income; }
            if ($diet     = $this->input('diet'))     { $where[] = "p.diet = ?"; $binds[] = $diet; }
            if ($dhosam   = $this->input('dhosam'))   { $where[] = "p.dhosam = ?"; $binds[] = $dhosam; }
            if ($star     = $this->input('star'))     { $where[] = "p.star LIKE ?"; $binds[] = "%$star%"; }
        }

        $whereStr = implode(' AND ', $where);

        $sql = "SELECT u.id, u.profile_id, u.name, u.gender, u.last_active,
                       p.age, p.city, p.state, p.religion, p.caste, p.education,
                       p.occupation, p.annual_income, p.marital_status, p.star, p.raasi,
                       p.dhosam, p.is_highlighted,
                       (SELECT ph.file_path FROM photos ph WHERE ph.user_id=u.id AND ph.is_primary=1
                        AND ph.is_approved='approved' LIMIT 1) AS photo,
                       (SELECT i.status FROM interests i
                        WHERE (i.sender_id=? AND i.receiver_id=u.id)
                           OR (i.sender_id=u.id AND i.receiver_id=?)
                        LIMIT 1) AS interest_status,
                       (SELECT 1 FROM shortlists sl WHERE sl.user_id=? AND sl.shortlisted_id=u.id LIMIT 1) AS is_shortlisted
                FROM users u
                JOIN profiles p ON p.user_id = u.id
                WHERE $whereStr
                ORDER BY p.is_highlighted DESC, u.last_active DESC";

        // Prepend interest-status bind params
        array_unshift($binds, $userId, $userId, $userId);

        $result    = Database::paginate($sql, $binds, $page, $this->perPage);
        $filters   = $_GET;
        $pageTitle = 'Search Results';

        $this->view(
            'Search/Views/results.php',
            array_merge($result, compact('filters', 'pageTitle', 'isAdvanced', 'plan')),
            'User/Views/layouts/main.php'
        );
    }

    public function advanced(array $params = []): void {
        $this->requireUser();
        $userId = Session::get('user_id');
        $plan   = $this->getUserPlan($userId);

        if (!$plan['advanced_search']) {
            Session::flash('info', 'Advanced search is available on Silver plan and above.');
            $this->redirect('subscription');
        }

        $pageTitle = 'Advanced Search';
        $this->view('Search/Views/advanced.php', compact('pageTitle', 'plan'), 'User/Views/layouts/main.php');
    }
}
