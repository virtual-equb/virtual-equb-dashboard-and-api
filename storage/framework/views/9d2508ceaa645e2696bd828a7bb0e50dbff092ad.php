<div class="terms-container">
    <h3 class="terms-title">Virtual Equb Terms and Conditions</h3>
    
    <?php $__currentLoopData = $terms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $term): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="term">
            <h4><?php echo e($index + 1); ?>. <?php echo e($term->title); ?></h4>
            <p><?php echo e($term->content); ?></p>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>

<style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f4f4f4;
        color: #333;
        margin: 0;
        padding: 20px;
    }

    .terms-container {
        max-width: 700px;
        max-height: 600px; /* Set a maximum height for scrolling */
        margin: 0 auto;
        padding: 20px;
        border: 1px solid #ccc;
        border-radius: 8px;
        background-color: #fff;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        overflow-y: auto; /* Enable vertical scrolling */
    }

    .terms-title {
        font-weight: bold;
        font-size: 24px;
        text-align: center;
        margin-bottom: 20px;
        color: #4CAF50; /* Green */
    }

    .term {
        margin-bottom: 20px;
        padding: 15px;
        border: 1px solid #e0e0e0;
        border-radius: 5px;
        background-color: #f9f9f9;
    }

    .term h4 {
        margin: 0;
        color: #333;
        font-weight: bold;
    }
</style><?php /**PATH D:\virtual Equb\virtual-backend\resources\views/admin/terms/termsCondition.blade.php ENDPATH**/ ?>