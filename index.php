<?php
require_once __DIR__ . '/layout/layout.php';
require_once __DIR__ . '/controllers/Controllers.php';

$studentController = new StudentController();
$feeController = new FeeController();

$students = $studentController->getAllStudents();
$totalStudents = $studentController->getStudentCount();
$totalCollectedAmount = $feeController->getTotalCollectedAmount();

$monthlyStats = $feeController->getMonthlyCollectionStats(date('Y'));
$classWiseStats = $studentController->getStudentCountByClass();

$months = FeeCollection::getMonths();

// Convert month numbers to names for display
foreach ($monthlyStats as &$stat) {
    $stat['month_name'] = $months[$stat['month']];
}

// Start output buffering to capture the content
ob_start();
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Dashboard</h1>
</div>

<!-- Stats Cards -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5 class="card-title">Total Students</h5>
                        <h2><?php echo $totalStudents; ?></h2>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-users fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5 class="card-title">Total Collected</h5>
                        <h2>₹<?php echo number_format($totalCollectedAmount, 2); ?></h2>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-rupee-sign fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5 class="card-title">This Month</h5>
                        <h2>₹<?php echo number_format($feeController->getTotalCollectedAmount(date('n'), date('Y')), 2); ?></h2>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-calendar fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="row mb-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Monthly Collection Stats (<?php echo date('Y'); ?>)</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Month</th>
                                <th>Amount</th>
                                <th>Payments</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($monthlyStats as $stat): ?>
                            <tr>
                                <td><?php echo $stat['month_name']; ?></td>
                                <td>₹<?php echo number_format($stat['total_amount'], 2); ?></td>
                                <td><?php echo $stat['total_payments']; ?></td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if (empty($monthlyStats)): ?>
                            <tr>
                                <td colspan="3" class="text-center">No fee collections yet</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Students by Class</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Class</th>
                                <th>Students</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($classWiseStats as $stat): ?>
                            <tr>
                                <td><?php echo $stat['class']; ?></td>
                                <td><?php echo $stat['count']; ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Students -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Recent Students</h5>
                <a href="students.php" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus me-1"></i>Manage Students
                </a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Roll No.</th>
                                <th>Name</th>
                                <th>Class</th>
                                <th>Parent Mobile</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $recentStudents = array_slice($students, 0, 10);
                            foreach ($recentStudents as $student): 
                            ?>
                            <tr>
                                <td><?php echo htmlspecialchars($student['roll_number']); ?></td>
                                <td><?php echo htmlspecialchars($student['name']); ?></td>
                                <td><?php echo htmlspecialchars($student['class']); ?></td>
                                <td><?php echo htmlspecialchars($student['parent_mobile']); ?></td>
                                <td>
                                    <a href="fee_collection.php?student_id=<?php echo $student['id']; ?>" class="btn btn-success btn-sm">
                                        <i class="fas fa-money-bill"></i>
                                    </a>
                                    <a href="student_fees.php?id=<?php echo $student['id']; ?>" class="btn btn-info btn-sm">
                                        <i class="fas fa-history"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
renderLayout('Dashboard', $content, 'dashboard');
?>
