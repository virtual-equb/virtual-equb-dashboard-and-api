<div class="terms-container">
    <h3 class="terms-title">Virtual Equb Terms and Conditions</h3>
    
    <?php $__currentLoopData = $terms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $term): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="term">
            <h4><?php echo e($index + 1); ?>. <?php echo e($term->title); ?></h4>
            <p><?php echo e($term->content); ?></p>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

    <div class="button-container">
        <button id="agreeButton" class="btn btn-agree">Agree</button>
        <button id="disagreeButton" class="btn btn-disagree">Don't Agree</button>
    </div>
</div>

<style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f4f4f4;
        color: #333;
    }

    .terms-container {
        max-width: 700px;
        margin: 40px auto;
        padding: 20px;
        border: 1px solid #ccc;
        border-radius: 8px;
        background-color: #fff;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
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

    .button-container {
        display: flex;
        justify-content: space-between;
        margin-top: 30px;
    }

    .btn {
        padding: 12px 20px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-weight: bold;
        transition: background-color 0.3s;
        font-size: 16px;
    }

    .btn-agree {
        background-color: #4CAF50; /* Green */
        color: white;
    }

    .btn-agree:hover {
        background-color: #45a049;
    }

    .btn-disagree {
        background-color: #f44336; /* Red */
        color: white;
    }

    .btn-disagree:hover {
        background-color: #e53935;
    }
</style>

<script>
    document.getElementById('agreeButton').addEventListener('click', function() {
        alert('You agreed to the terms and conditions.');
        // Add further logic for agreeing
    });

    document.getElementById('disagreeButton').addEventListener('click', function() {
        alert('You did not agree to the terms and conditions.');
        // Add further logic for disagreeing
    });
</script><?php /**PATH D:\virtual Equb\virtual-backend\resources\views/admin/terms/termsCondition.blade.php ENDPATH**/ ?>