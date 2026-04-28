</div>
    </div>
</div>

<script src="/assets/js/main.js"></script>
<?php if (isset($extra_js)): ?>
    <?php foreach ($extra_js as $js): ?>
        <script src="/assets/js/<?php echo $js; ?>"></script>
    <?php endforeach; ?>
<?php endif; ?>
</body>
</html>