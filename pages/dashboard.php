<?php
/**
 * Simple Dashboard Page Template
 */

// Get statistics from database
try {
    $stmt = $GLOBALS['db']->prepare("SELECT COUNT(*) as total_personel FROM personel WHERE is_active = 1");
    $stmt->execute();
    $personelCount = $stmt->fetch()['total_personel'];
    
    $stmt = $GLOBALS['db']->prepare("SELECT COUNT(*) as total_operations FROM operations WHERE is_active = 1");
    $stmt->execute();
    $operationsCount = $stmt->fetch()['total_operations'];
    
} catch (Exception $e) {
    $personelCount = 0;
    $operationsCount = 0;
}
?>


<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Personel</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo number_format($personelCount); ?></div>
                        <small class="text-muted">Database Real-time</small>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-users fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Operasi Aktif</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo number_format($operationsCount); ?></div>
                        <small class="text-muted">Database Real-time</small>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-cogs fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">User Role</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo ucfirst($GLOBALS['user_role']); ?></div>
                        <small class="text-muted">Current Session</small>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-user fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">System Status</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">Online</div>
                        <small class="text-muted">All Systems OK</small>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-server fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Quick Actions</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <button class="btn btn-primary btn-block">
                            <i class="fas fa-user-plus"></i> Tambah Personel
                        </button>
                    </div>
                    <div class="col-md-3 mb-3">
                        <button class="btn btn-success btn-block">
                            <i class="fas fa-plus"></i> Buat Operasi
                        </button>
                    </div>
                    <div class="col-md-3 mb-3">
                        <button class="btn btn-info btn-block">
                            <i class="fas fa-file-alt"></i> Laporan Harian
                        </button>
                    </div>
                    <div class="col-md-3 mb-3">
                        <button class="btn btn-warning btn-block">
                            <i class="fas fa-chart-bar"></i> Analytics
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    console.log('Dashboard loaded from template: dashboard.php');
    console.log('Database stats:', {
        personel: <?php echo $personelCount; ?>,
        operations: <?php echo $operationsCount; ?>,
        userRole: '<?php echo $GLOBALS['user_role']; ?>'
    });
});
</script>
