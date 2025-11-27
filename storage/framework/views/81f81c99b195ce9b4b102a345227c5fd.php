
<div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 row-cols-xl-5 g-4">
    <?php $__currentLoopData = $vehicles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $vehicle): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="col">
            <div class="card h-100 shadow-sm">
                <div class="position-relative" style="overflow: hidden;">
                    
                    <?php if($vehicle->images->isNotEmpty()): ?>
                        <div id="carousel-<?php echo e($vehicle->id); ?>" class="carousel slide" data-ride="carousel">
                            <div class="carousel-inner" style="height: 200px;">
                                <?php $__currentLoopData = $vehicle->images->sortBy('id'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $image): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="carousel-item <?php echo e($index == 0 ? 'active' : ''); ?>" style="height: 200px;">
                                        <img class="d-block w-100 h-100 object-fit-cover" src="<?php echo e(asset('storage/' . $image->image)); ?>" alt="Imagen <?php echo e($index + 1); ?>">
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                            <?php if($vehicle->images->count() > 1): ?>
                                <a class="carousel-control-prev" href="#carousel-<?php echo e($vehicle->id); ?>" role="button" data-slide="prev">
                                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                    <span class="sr-only">Previous</span>
                                </a>
                                <a class="carousel-control-next" href="#carousel-<?php echo e($vehicle->id); ?>" role="button" data-slide="next">
                                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                    <span class="sr-only">Next</span>
                                </a>
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                        <img src="<?php echo e(asset('storage/vehicles/noimage.jpg')); ?>" class="card-img-top" alt="Imagen del vehículo" style="height: 200px; object-fit: cover;">
                    <?php endif; ?>
                    <?php if(!empty($vehicle->plate)): ?>
                        <span class="vehicle-plate"><?php echo e($vehicle->plate); ?></span>
                    <?php endif; ?>
                </div>
                <div class="card-body py-3 d-flex justify-content-between align-items-start">
                    <div class="vehicle-info">
                        
                        <div class="d-flex align-items-center mb-2 gap-2">
                            <h6 class="mb-0 fw-bold text-truncate" style="max-width:180px;"><?php echo e($vehicle->name ?? ($vehicle->model->brand->name ?? 'Marca') . ' ' . ($vehicle->model->name ?? 'Modelo')); ?></h6>
                            <span class="badge bg-secondary"><?php echo e($vehicle->type->name ?? 'Tipo'); ?></span>
                            <?php if($vehicle->status == 1): ?>
                                <span class="badge bg-success">Activo</span>
                            <?php else: ?>
                                <span class="badge bg-danger">Inactivo</span>
                            <?php endif; ?>
                        </div>

                        <div class="mb-1 text-muted small">Modelo: <?php echo e($vehicle->model->brand->name ?? 'Marca'); ?> <?php echo e($vehicle->model->name ?? 'Modelo'); ?></div>

                        <div class="text-muted small">Categoría: <?php echo e($vehicle->color->name ?? 'N/A'); ?></div>
                    </div>

                    
                    <div class="text-end">
                        <div class="vehicle-year fw-bold"><?php echo e($vehicle->year ?? '2025'); ?></div>
                    </div>
                </div>                            <div class="card-footer bg-white border-0 d-flex justify-content-end gap-2">
                    <form action="<?php echo e(route('admin.vehicles.destroy', $vehicle)); ?>" method="POST" class="frmDelete d-inline">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('DELETE'); ?>
                        <button type="submit" class="btn btn-sm btn-outline-danger">Eliminar</button>
                    </form>
                    <button class="btn btn-sm btn-dark btnEditar" id="<?php echo e($vehicle->id); ?>">Editar</button>
                </div>
            </div>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>
<?php /**PATH F:\USAT 2025-2\topicos\PryFINAL\PryRsuReciclaje\resources\views/admin/vehicles/partials/grid.blade.php ENDPATH**/ ?>