<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="board-title">게시글 목록</h2>
    <?php if (isset($_SESSION['user_id'])): ?>
        <a href="/write" class="btn btn-primary">글쓰기</a>
    <?php endif; ?>
</div>

<div class="table-responsive">
    <table class="table table-hover">
        <thead class="table-light">
            <tr>
                <th style="width: 10%">번호</th>
                <th style="width: 50%">제목</th>
                <th style="width: 15%">작성자</th>
                <th style="width: 10%">조회</th>
                <th style="width: 10%">추천</th>
                <th style="width: 15%">작성일</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($posts)): ?>
                <tr>
                    <td colspan="6" class="text-center py-4">게시글이 없습니다.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($posts as $post): ?>
                    <tr>
                        <td><?= $post['id'] ?></td>
                        <td>
                            <a href="/view/<?= $post['id'] ?>" class="text-decoration-none text-dark">
                                <?= htmlspecialchars($post['title']) ?>
                                <?php if ($post['comment_count'] > 0): ?>
                                    <span class="text-primary">[<?= $post['comment_count'] ?>]</span>
                                <?php endif; ?>
                            </a>
                        </td>
                        <td><?= htmlspecialchars($post['username']) ?></td>
                        <td><?= $post['view_count'] ?></td>
                        <td><?= $post['like_count'] ?></td>
                        <td><?= date('Y-m-d', strtotime($post['created_at'])) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php if ($total_pages > 1): ?>
<nav aria-label="Page navigation" class="mt-4">
    <ul class="pagination justify-content-center">
        <?php if ($current_page > 1): ?>
            <li class="page-item">
                <a class="page-link" href="/?page=<?= $current_page - 1 ?>" aria-label="Previous">
                    <span aria-hidden="true">&laquo;</span>
                </a>
            </li>
        <?php endif; ?>

        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <li class="page-item <?= $i === $current_page ? 'active' : '' ?>">
                <a class="page-link" href="/?page=<?= $i ?>"><?= $i ?></a>
            </li>
        <?php endfor; ?>

        <?php if ($current_page < $total_pages): ?>
            <li class="page-item">
                <a class="page-link" href="/?page=<?= $current_page + 1 ?>" aria-label="Next">
                    <span aria-hidden="true">&raquo;</span>
                </a>
            </li>
        <?php endif; ?>
    </ul>
</nav>
<?php endif; ?> 