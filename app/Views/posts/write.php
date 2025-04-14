<?php
$title = "게시글 작성";
?>

<div class="container mt-5">
    <h2 class="mb-4">게시글 작성</h2>
    
    <?php if (Session::has('error')): ?>
        <div class="alert alert-danger">
            <?= Session::flash('error') ?>
        </div>
    <?php endif; ?>
    
    <form action="/write" method="POST">
        <div class="mb-3">
            <label for="title" class="form-label">제목</label>
            <input type="text" class="form-control" id="title" name="title" required>
        </div>
        
        <div class="mb-3">
            <label for="content" class="form-label">내용</label>
            <textarea class="form-control" id="content" name="content" rows="10" required></textarea>
        </div>
        
        <div class="mb-3">
            <button type="submit" class="btn btn-primary">작성하기</button>
            <a href="/" class="btn btn-secondary">취소</a>
        </div>
    </form>
</div> 