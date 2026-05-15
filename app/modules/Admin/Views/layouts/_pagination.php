<?php
/**
 * Reusable pagination partial
 * Required vars (extracted from paginate() result): $current_page, $last_page, $total, $per_page
 */
$current_page = (int)($current_page ?? 1);
$last_page    = (int)($last_page    ?? 1);
$total        = (int)($total        ?? 0);
$per_page     = (int)($per_page     ?? 20);
$query        = $_GET;
?>
<?php if ($last_page > 1): ?>
<div class="d-flex align-items-center justify-content-between mt-3 flex-wrap gap-2">
  <small class="text-muted">
    Showing
    <?= number_format(($current_page - 1) * $per_page + 1) ?>–<?= number_format(min($current_page * $per_page, $total)) ?>
    of <strong><?= number_format($total) ?></strong> records
  </small>
  <nav aria-label="Pagination">
    <ul class="pagination pagination-sm mb-0">

      <!-- Previous -->
      <li class="page-item <?= $current_page <= 1 ? 'disabled' : '' ?>">
        <?php $query['page'] = $current_page - 1; ?>
        <a class="page-link" href="?<?= http_build_query($query) ?>">
          <i class="bi bi-chevron-left"></i>
        </a>
      </li>

      <!-- Page numbers -->
      <?php
      $range = 2;
      $start = max(1, $current_page - $range);
      $end   = min($last_page, $current_page + $range);

      if ($start > 1):
          $query['page'] = 1; ?>
        <li class="page-item">
          <a class="page-link" href="?<?= http_build_query($query) ?>">1</a>
        </li>
        <?php if ($start > 2): ?>
        <li class="page-item disabled"><span class="page-link">…</span></li>
        <?php endif;
      endif;

      for ($i = $start; $i <= $end; $i++):
          $query['page'] = $i; ?>
        <li class="page-item <?= $i === $current_page ? 'active' : '' ?>">
          <a class="page-link" href="?<?= http_build_query($query) ?>"><?= $i ?></a>
        </li>
      <?php endfor;

      if ($end < $last_page):
          if ($end < $last_page - 1): ?>
          <li class="page-item disabled"><span class="page-link">…</span></li>
          <?php endif;
          $query['page'] = $last_page; ?>
        <li class="page-item">
          <a class="page-link" href="?<?= http_build_query($query) ?>"><?= $last_page ?></a>
        </li>
      <?php endif; ?>

      <!-- Next -->
      <?php $query['page'] = $current_page + 1; ?>
      <li class="page-item <?= $current_page >= $last_page ? 'disabled' : '' ?>">
        <a class="page-link" href="?<?= http_build_query($query) ?>">
          <i class="bi bi-chevron-right"></i>
        </a>
      </li>

    </ul>
  </nav>
</div>
<?php else: ?>
<div class="mt-2">
  <small class="text-muted"><?= number_format($total) ?> record<?= $total !== 1 ? 's' : '' ?></small>
</div>
<?php endif; ?>
