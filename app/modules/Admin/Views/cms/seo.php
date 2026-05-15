<?php // ═══ SEO PAGES ════════════════════════════════════════
$pageTitle = 'SEO Pages'; ?>

<div class="d-flex align-items-center justify-content-between mb-3">
  <h5 class="fw-bold mb-0"><i class="bi bi-search me-2 text-info"></i>SEO Community Pages</h5>
  <a href="<?= APP_URL ?>/admin/cms/seo?edit=new" class="btn btn-sm btn-primary">
    <i class="bi bi-plus-lg me-1"></i>Add Page
  </a>
</div>

<div class="filter-bar mb-3">
  <form method="GET" class="row g-2 align-items-end">
    <div class="col-sm-6 col-md-5">
      <div class="input-group input-group-sm">
        <span class="input-group-text"><i class="bi bi-search"></i></span>
        <input type="text" name="search" class="form-control" placeholder="Slug, caste key, title…" value="<?= htmlspecialchars($filters['search']) ?>">
      </div>
    </div>
    <div class="col-auto d-flex gap-2">
      <button class="btn btn-primary btn-sm"><i class="bi bi-funnel"></i></button>
      <a href="<?= APP_URL ?>/admin/cms/seo" class="btn btn-outline-secondary btn-sm"><i class="bi bi-x"></i></a>
    </div>
  </form>
</div>

<div class="card table-card">
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover mb-0">
        <thead><tr>
          <th class="ps-4">Slug</th><th>Caste Key</th><th>Title (EN)</th><th>Status</th><th>Order</th><th>Updated</th><th class="text-center">Edit</th>
        </tr></thead>
        <tbody>
        <?php if(empty($data)): ?>
          <tr><td colspan="7" class="text-center text-muted py-5"><i class="bi bi-inbox fs-2 d-block mb-2"></i>No SEO pages yet.</td></tr>
        <?php else: foreach($data as $p): ?>
        <tr>
          <td class="ps-4"><code style="font-size:.78rem">/<?= htmlspecialchars($p['slug']) ?></code></td>
          <td><small><?= htmlspecialchars($p['caste_key']??'—') ?></small></td>
          <td style="max-width:220px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap"><small><?= htmlspecialchars($p['title_en']??'—') ?></small></td>
          <td><?= $p['is_published']?'<span class="badge badge-approved">Live</span>':'<span class="badge badge-pending">Draft</span>' ?></td>
          <td><small><?= $p['sort_order'] ?></small></td>
          <td><small class="text-muted"><?= date('d M y',strtotime($p['updated_at'])) ?></small></td>
          <td class="text-center">
            <a href="?edit=<?= $p['id'] ?>" class="btn btn-xs btn-outline-primary py-0 px-2" style="font-size:.75rem"><i class="bi bi-pencil"></i></a>
          </td>
        </tr>
        <?php endforeach; endif; ?>
        </tbody>
      </table>
    </div>
  </div>
  <div class="card-footer bg-white border-0 px-4"><?php include __DIR__.'/../layouts/_pagination.php'; ?></div>
</div>
