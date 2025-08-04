<?php
require_once __DIR__ . '/controllers/Controllers.php';

$studentController = new StudentController();
$feeController = new FeeController();

$studentId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$studentId) {
    header('Location: students.php');
    exit;
}

$student = $studentController->getStudentById($studentId);
if (!$student) {
    header('Location: students.php');
    exit;
}

$feeHistory = $feeController->getFeeHistoryByStudent($studentId);
$months = FeeCollection::getMonths();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fee History - <?php echo htmlspecialchars($student['name']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-graduation-cap me-2"></i>
                School Fee System
            </a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="index.php">Dashboard</a>
                <a class="nav-link" href="students.php">Students</a>
                <a class="nav-link" href="fee_collection.php">Fee Collection</a>
                <a class="nav-link" href="reports.php">Reports</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row mb-4">
            <div class="col-md-8">
                <h1>Fee History</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="students.php">Students</a></li>
                        <li class="breadcrumb-item active"><?php echo htmlspecialchars($student['name']); ?></li>
                    </ol>
                </nav>
            </div>
            <div class="col-md-4 text-end">
                <a href="fee_collection.php?student_id=<?php echo $student['id']; ?>" class="btn btn-success">
                    <i class="fas fa-plus me-1"></i>Collect Fee
                </a>
            </div>
        </div>

        <!-- Student Information -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Student Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <strong>Name:</strong><br>
                                <?php echo htmlspecialchars($student['name']); ?>
                            </div>
                            <div class="col-md-3">
                                <strong>Roll Number:</strong><br>
                                <?php echo htmlspecialchars($student['roll_number']); ?>
                            </div>
                            <div class="col-md-3">
                                <strong>Class:</strong><br>
                                <?php echo htmlspecialchars($student['class']); ?>
                            </div>
                            <div class="col-md-3">
                                <strong>Parent Mobile:</strong><br>
                                <?php echo htmlspecialchars($student['parent_mobile']); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Fee Summary -->
        <?php
        $totalPaid = 0;
        $totalPayments = count($feeHistory);
        foreach ($feeHistory as $fee) {
            $totalPaid += $fee['amount_paid'];
        }
        ?>
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <h5 class="card-title">Total Paid</h5>
                        <h2>₹<?php echo number_format($totalPaid, 2); ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <h5 class="card-title">Total Payments</h5>
                        <h2><?php echo $totalPayments; ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <h5 class="card-title">Average Payment</h5>
                        <h2>₹<?php echo $totalPayments > 0 ? number_format($totalPaid / $totalPayments, 2) : '0.00'; ?></h2>
                    </div>
                </div>
            </div>
        </div>

        <!-- Fee History Table -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Payment History</h5>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($feeHistory)): ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Month/Year</th>
                                        <th>Amount Paid</th>
                                        <th>Payment Date</th>
                                        <th>Payment Mode</th>
                                        <th>Remarks</th>
                                        <th>Collection Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($feeHistory as $fee): ?>
                                    <tr>
                                        <td>
                                            <strong><?php echo $months[$fee['month']]; ?> <?php echo $fee['year']; ?></strong>
                                        </td>
                                        <td>
                                            <span class="badge bg-success">₹<?php echo number_format($fee['amount_paid'], 2); ?></span>
                                        </td>
                                        <td><?php echo date('d/m/Y', strtotime($fee['payment_date'])); ?></td>
                                        <td>
                                            <span class="badge bg-<?php 
                                                echo $fee['payment_mode'] == 'Cash' ? 'primary' : 
                                                    ($fee['payment_mode'] == 'Online' ? 'info' : 'secondary'); 
                                            ?>">
                                                <?php echo htmlspecialchars($fee['payment_mode']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo htmlspecialchars($fee['remarks']) ?: '-'; ?></td>
                                        <td><?php echo date('d/m/Y H:i', strtotime($fee['created_at'])); ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php else: ?>
                        <div class="text-center py-5">
                            <i class="fas fa-receipt fa-3x text-muted mb-3"></i>
                            <h5>No fee payments found</h5>
                            <p class="text-muted">This student hasn't made any fee payments yet.</p>
                            <a href="fee_collection.php?student_id=<?php echo $student['id']; ?>" class="btn btn-primary">
                                <i class="fas fa-plus me-1"></i>Collect First Payment
                            </a>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Fee Payment Calendar -->
        <?php if (!empty($feeHistory)): ?>
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Payment Calendar</h5>
                    </div>
                    <div class="card-body">
                        <?php
                        // Group payments by year
                        $paymentsByYear = [];
                        foreach ($feeHistory as $fee) {
                            $paymentsByYear[$fee['year']][$fee['month']] = $fee;
                        }
                        
                        foreach ($paymentsByYear as $year => $yearPayments):
                        ?>
                        <h6 class="mt-3 mb-2"><?php echo $year; ?></h6>
                        <div class="row">
                            <?php for ($month = 1; $month <= 12; $month++): ?>
                            <div class="col-md-1 mb-2">
                                <div class="text-center">
                                    <small class="d-block mb-1"><?php echo substr($months[$month], 0, 3); ?></small>
                                    <?php if (isset($yearPayments[$month])): ?>
                                    <div class="badge bg-success w-100">
                                        ₹<?php echo number_format($yearPayments[$month]['amount_paid'], 0); ?>
                                    </div>
                                    <?php else: ?>
                                    <div class="badge bg-light text-dark w-100">-</div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php endfor; ?>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
