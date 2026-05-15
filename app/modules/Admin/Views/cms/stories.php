<?php // ═══ SUCCESS STORIES ═════════════════════════════════
$pageTitle = 'Success Stories'; ?>

<div class="d-flex align-items-center justify-content-between mb-3">
  <h5 class="fw-bold mb-0"><i class="bi bi-hearts me-2 text-danger"></i>Success Stories</h5>
  <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#storyModal" data-id="0">
    <i class="bi bi-plus-lg me-1"></i>Add Story
  </button>
</div>

<?php if(isset($flash) && $flash): ?>
<div class="alert alert-success alert-dismissible fade show" role="alert">
  <i class="bi bi-check-circle-fill me-2"></i><?= htmlspecialchars($flash) ?>
  <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<div class="filter-bar mb-3">
  <form method="GET" class="row g-2 align-items-end">
    <div class="col-sm-6 col-md-5">
      <div class="input-group input-group-sm">
        <span class="input-group-text"><i class="bi bi-search"></i></span>
        <input type="text" name="search" class="form-control" placeholder="Bride or Groom name…" value="<?= htmlspecialchars($filters['search']) ?>">
      </div>
    </div>
    <div class="col-auto d-flex gap-2">
      <button class="btn btn-primary btn-sm"><i class="bi bi-funnel"></i></button>
      <a href="<?= APP_URL ?>/admin/cms/stories" class="btn btn-outline-secondary btn-sm"><i class="bi bi-x"></i></a>
    </div>
  </form>
</div>

<div class="card table-card">
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover mb-0">
        <thead><tr>
          <th class="ps-4">Couple</th><th>Married On</th><th>Featured</th><th>Published</th><th>Added</th><th class="text-center">Actions</th>
        </tr></thead>
        <tbody>
        <?php if(empty($data)): ?>
          <tr><td colspan="6" class="text-center text-muted py-5"><i class="bi bi-inbox fs-2 d-block mb-2"></i>No stories yet.</td></tr>
        <?php else: foreach($data as $s): ?>
        <tr>
          <td class="ps-4">
            <div class="fw-semibold" style="font-size:.86rem"><?= htmlspecialchars($s['bride_name']) ?> &amp; <?= htmlspecialchars($s['groom_name']) ?></div>
            <small class="text-muted"><?= htmlspecialchars(substr($s['story']??'',0,60)).(strlen($s['story']??'')>60?'…':'') ?></small>
          </td>
          <td><small><?= $s['married_on']?date('d M Y',strtotime($s['married_on'])):'—' ?></small></td>
          <td><?= $s['is_featured']?'<span class="badge bg-warning text-dark">Featured</span>':'<span class="text-muted">—</span>' ?></td>
          <td><?= $s['is_published']?'<span class="badge badge-approved">Live</span>':'<span class="badge badge-pending">Draft</span>' ?></td>
          <td><small class="text-muted"><?= date('d M y',strtotime($s['created_at'])) ?></small></td>
          <td class="text-center">
            <div class="d-flex gap-1 justify-content-center">
              <button class="btn btn-xs btn-outline-primary py-0 px-2" style="font-size:.75rem"
                data-bs-toggle="modal" data-bs-target="#storyModal"
                data-id="<?= $s['id'] ?>" data-bride="<?= htmlspecialchars($s['bride_name']) ?>"
                data-groom="<?= htmlspecialchars($s['groom_name']) ?>" data-story="<?= htmlspecialchars($s['story']??'') ?>"
                data-married="<?= $s['married_on'] ?>" data-featured="<?= $s['is_featured'] ?>" data-published="<?= $s['is_published'] ?>">
                <i class="bi bi-pencil"></i>
              </button>
              <form method="POST" action="<?= APP_URL ?>/admin/cms/stories/delete" class="d-inline">
                <input type="hidden" name="_csrf" value="<?= $csrf ?>">
                <input type="hidden" name="id" value="<?= $s['id'] ?>">
                <button class="btn btn-xs btn-outline-danger py-0 px-2" style="font-size:.75rem" data-confirm="Delete this story?"><i class="bi bi-trash"></i></button>
              </form>
            </div>
          </td>
        </tr>
        <?php endforeach; endif; ?>
        </tbody>
      </table>
    </div>
  </div>
  <div class="card-footer bg-white border-0 px-4"><?php include __DIR__.'/../layouts/_pagination.php'; ?></div>
</div>

<!-- Story Modal -->
<div class="modal fade" id="storyModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header"><h6 class="modal-title">Success Story</h6><button class="btn-close" data-bs-dismiss="modal"></button></div>
      <form method="POST" action="<?= APP_URL ?>/admin/cms/stories/save">
        <input type="hidden" name="_csrf" value="<?= $csrf ?>">
        <input type="hidden" name="id" id="storyId" value="0">
        <div class="modal-body">
          <div class="row g-2">
            <div class="col-6">
              <label class="form-label form-label-sm fw-semibold">Bride Name *</label>
              <input type="text" name="bride_name" id="storyBride" class="form-control form-control-sm" required>
            </div>
            <div class="col-6">
              <label class="form-label form-label-sm fw-semibold">Groom Name *</label>
              <input type="text" name="groom_name" id="storyGroom" class="form-control form-control-sm" required>
            </div>
            <div class="col-12">
              <label class="form-label form-label-sm fw-semibold">Story</label>
              <textarea name="story" id="storyText" class="form-control form-control-sm" rows="4"></textarea>
            </div>
            <div class="col-6">
              <label class="form-label form-label-sm fw-semibold">Married On</label>
              <input type="date" name="married_on" id="storyMarried" class="form-control form-control-sm">
            </div>
            <div class="col-3 d-flex align-items-end pb-1">
              <div class="form-check">
                <input type="checkbox" name="is_featured" id="storyFeatured" class="form-check-input" value="1">
                <label class="form-check-label small" for="storyFeatured">Featured</label>
              </div>
            </div>
            <div class="col-3 d-flex align-items-end pb-1">
              <div class="form-check">
                <input type="checkbox" name="is_published" id="storyPublished" class="form-check-input" value="1">
                <label class="form-check-label small" for="storyPublished">Publish</label>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-sm btn-primary">Save</button>
        </div>
      </form>
    </div>
  </div>
</div>
<script>
document.getElementById('storyModal')?.addEventListener('show.bs.modal', function(e){
  const d = e.relatedTarget?.dataset || {};
  this.querySelector('#storyId').value       = d.id || '0';
  this.querySelector('#storyBride').value    = d.bride || '';
  this.querySelector('#storyGroom').value    = d.groom || '';
  this.querySelector('#storyText').value     = d.story || '';
  this.querySelector('#storyMarried').value  = d.married || '';
  this.querySelector('#storyFeatured').checked   = d.featured === '1';
  this.querySelector('#storyPublished').checked  = d.published === '1';
});
</script>
