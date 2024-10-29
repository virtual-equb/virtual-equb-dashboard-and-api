<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo e($title); ?></title>
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <link rel="stylesheet" href="<?php echo e(url('plugins/fontawesome-free/css/all.min.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(url('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(url('plugins/datatables-responsive/css/responsive.bootstrap4.min.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(url('plugins/datatables-buttons/css/buttons.bootstrap4.min.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(url('dist/css/adminlte.min.css')); ?>">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    </link>
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.standalone.min.css"
        integrity="sha512-TQQ3J4WkE/rwojNFo6OJdyu6G8Xe9z8rMrlF9y7xpFbQfW5g8aSWcygCQ4vqRiJqFsDsE1T6MoAOMJkFXlrI9A=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css"
        integrity="sha512-mSYUmp1HYZDFaVKK//63EcZq4iFWFjxSL+Z3T/aCt4IO9Cejm03q3NKKYN6pFQzY0SBOr8h+eCIAZHPXcpZaNw=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <link rel="stylesheet" href="<?php echo e(url('plugins/jqvmap/jqvmap.min.css')); ?>">
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.css">
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker-standalone.css">
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker-standalone.min.css">
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.20/summernote-bs4.min.css"
        integrity="sha512-ngQ4IGzHQ3s/Hh8kMyG4FC74wzitukRMIcTOoKT3EyzFZCILOPF0twiXOQn75eDINUfKBYmzYn2AA8DkAk8veQ=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="shortcut icon" href="<?php echo e(asset('dist/img/SVG/VirtualEqubLogoIcon.svg')); ?>" sizes="32x32"
        type="image/x-icon" />
    <?php if (! empty(trim($__env->yieldContent('styles')))): ?>
        <?php echo $__env->yieldContent('styles'); ?>
    <?php endif; ?>
</head>

<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">

        <?php echo $__env->make('partials.navbar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

        <?php echo $__env->make('partials.sidebar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

        <?php $__env->startSection('messageSection'); ?>
            <section class="pt-2 " style="margin-left:190px;">
                <div class="row d-flex justify-content-center">
                    <div class="col-md-10" id="messages">

                        <?php if($errors->any()): ?>
                            <div class="alert alert-danger bg-danger">
                                <ul>
                                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <li><?php echo e($error); ?></li>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </ul>
                            </div>
                        <?php endif; ?>

                        <?php if(Session::get('success')): ?>
                            <div class="alert alert-success alert-block " style="background-color: #00843d; color: white;">
                                <button type="button" class="close" data-dismiss="alert">×</button>
                                <strong><?php echo e(Session::get('success')); ?></strong>
                            </div>
                        <?php elseif(Session::get('error')): ?>
                            <div class="alert alert-error alert-block" style="background-color: #d22630;color: white;">
                                <button type="button" class="close" data-dismiss="alert">×</button>
                                <strong><?php echo e(Session::get('error')); ?></strong>
                            </div>
                        <?php endif; ?>

                    </div>
                </div>
            </section>
        <?php echo $__env->yieldSection(); ?>


        <?php if (! empty(trim($__env->yieldContent('content')))): ?>
            <?php echo $__env->yieldContent('content'); ?>
        <?php endif; ?>
        <?php echo $__env->make('partials.footer', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    </div>
    <script src="<?php echo e(url('plugins/jquery/jquery.min.js')); ?>"></script>
    <script type="text/javascript" src="<?php echo e(url('plugins/loadingOverlay/loadingoverlay.min.js')); ?>"></script>
    <script src="<?php echo e(url('plugins/bootstrap/js/bootstrap.bundle.min.js')); ?>"></script>
    <script src="<?php echo e(url('plugins/chart.js/Chart.min.js')); ?>"></script>
    <script src="<?php echo e(url('plugins/datatables/jquery.dataTables.min.js')); ?>"></script>
    <script src="<?php echo e(url('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js')); ?>"></script>
    <script src="<?php echo e(url('plugins/datatables-responsive/js/dataTables.responsive.min.js')); ?>"></script>
    <script src="<?php echo e(url('plugins/datatables-responsive/js/responsive.bootstrap4.min.js')); ?>"></script>
    <script src="<?php echo e(url('plugins/datatables-buttons/js/dataTables.buttons.min.js')); ?>"></script>
    <script src="<?php echo e(url('plugins/datatables-buttons/js/buttons.bootstrap4.min.js')); ?>"></script>
    <script src="<?php echo e(url('plugins/jszip/jszip.min.js')); ?>"></script>
    <script src="<?php echo e(url('plugins/pdfmake/pdfmake.min.js')); ?>"></script>
    <script src="<?php echo e(url('plugins/pdfmake/vfs_fonts.js')); ?>"></script>
    <script src="<?php echo e(url('plugins/datatables-buttons/js/buttons.html5.min.js')); ?>"></script>
    <script src="<?php echo e(url('plugins/datatables-buttons/js/buttons.print.min.js')); ?>"></script>
    <script src="<?php echo e(url('plugins/datatables-buttons/js/buttons.colVis.min.js')); ?>"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"
        integrity="sha512-T/tUfKSV1bihCnd+MxKD0Hm1uBBroVYBOYSk1knyvQ9VyZJpc/ALb4P0r6ubwVPSGB2GvjeoMAJJImBG12TiaQ=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="<?php echo e(url('dist/js/adminlte.min.js')); ?>"></script>
    <script src="<?php echo e(url('dist/js/bootstrapValidator.min.js')); ?>"></script>
    <script type="text/javascript"
        src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-validator/0.5.3/js/bootstrapValidator.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.15.1/moment.min.js"></script>
    <script
        src="//cdn.rawgit.com/Eonasdan/bootstrap-datetimepicker/e8bddc60e73c1ec2475f827be36e1957af72e2ea/src/js/bootstrap-datetimepicker.js">
    </script>


    <script src="<?php echo e(url('plugins/jquery-validation/jquery.validate.min.js')); ?>"></script>
    <script src="<?php echo e(url('plugins/jquery-validation/additional-methods.min.js')); ?>"></script>
    <script>
        $('#messages').fadeOut(25000);
    </script>
    <script type="text/javascript">
        var loadHere = '<br><div id="loading" class="row d-flex justify-content-center"><div class="row"><img src="' +
            "<?php echo e(url('images/loading.gif')); ?>" + '"/></div></div>';
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.20/summernote.min.js"
        integrity="sha512-6rE6Bx6fCBpRXG/FWpQmvguMWDLWMQjPycXMr35Zx/HRD9nwySZswkkLksgyQcvrpYMx0FELLJVBvWFtubZhDQ=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <?php if (! empty(trim($__env->yieldContent('scripts')))): ?>
        <?php echo $__env->yieldContent('scripts'); ?>
    <?php endif; ?>
</body>

</html>
<?php /**PATH D:\virtual Equb\virtual-backend\resources\views/layouts/app.blade.php ENDPATH**/ ?>