<?php
require_once __DIR__ . '/controllers/Controllers.php';

$studentController = new StudentController();
$feeController = new FeeController();

// Get filter parameters
$filterMonth = isset($_GET['month']) ? (int)$_GET['month'] : date('n');
$filterYear = isset($_GET['year']) ? (int)$_GET['year'] : date('Y');
$filterClass = isset($_GET['class']) ? $_GET['class'] : '';

// Get data based on filters
$totalStudents = $studentController->getStudentCount();
$totalCollected = $feeController->getTotalCollectedAmount();
$monthlyTotal = $feeController->getTotalCollectedAmount($filterMonth, $filterYear);

$monthlyStats = $feeController->getMonthlyCollectionStats($filterYear);
$classWiseStats = $feeController->getClassWiseCollectionStats($filterMonth, $filterYear);
$studentsByClass = $studentController->getStudentCountByClass();

// Get all collections for the selected month/year
$collections = $feeController->getAllFeeCollections();
if ($filterMonth && $filterYear) {
    $collections = array_filter($collections, function($collection) use ($filterMonth, $filterYear) {
        return $collection['month'] == $filterMonth && $collection['year'] == $filterYear;
    });
}

$months = FeeCollection::getMonths();
$classes = Student::getClasses();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports - School Fee Collection System</title>
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
                <a class="nav-link active" href="reports.php">Reports</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row mb-4">
            <div class="col-md-8">
                <h1>Reports & Analytics</h1>
            </div>
            <div class="col-md-4">
                <button class="btn btn-success" onclick="printReport()">
                    <i class="fas fa-print me-1"></i>Print Report
                </button>
            </div>
        </div>

        <!-- Filters -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Filters</h5>
                    </div>
                    <div class="card-body">
                        <form method="GET" class="row g-3">
                            <div class="col-md-3">
                                <label for="month" class="form-label">Month</label>
                                <select class="form-control" name="month">
                                    <option value="">All Months</option>
                                    <?php foreach ($months as $num => $name): ?>
                                    <option value="<?php echo $num; ?>" <?php echo ($filterMonth == $num) ? 'selected' : ''; ?>>
                                        <?php echo $name; ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="year" class="form-label">Year</label>
                                <select class="form-control" name="year">
                                    <?php for ($y = date('Y') + 1; $y >= 2020; $y--): ?>
                                    <option value="<?php echo $y; ?>" <?php echo ($filterYear == $y) ? 'selected' : ''; ?>>
                                        <?php echo $y; ?>
                                    </option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="class" class="form-label">Class</label>
                                <select class="form-control" name="class">
                                    <option value="">All Classes</option>
                                    <?php foreach ($classes as $class): ?>
                                    <option value="<?php echo $class; ?>" <?php echo ($filterClass == $class) ? 'selected' : ''; ?>>
                                        <?php echo $class; ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">&nbsp;</label>
                                <div>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-filter me-1"></i>Apply Filters
                                    </button>
                                    <a href="reports.php" class="btn btn-outline-secondary">
                                        <i class="fas fa-times"></i>
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="card-title">Total Students</h6>
                                <h3><?php echo $totalStudents; ?></h3>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-users fa-2x opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="card-title">Total Collected</h6>
                                <h3>₹<?php echo number_format($totalCollected, 0); ?></h3>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-rupee-sign fa-2x opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="card-title">This Month</h6>
                                <h3>₹<?php echo number_format($monthlyTotal, 0); ?></h3>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-calendar fa-2x opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="card-title">Collections</h6>
                                <h3><?php echo count($collections); ?></h3>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-receipt fa-2x opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts and Tables -->
        <div class="row mb-4">
            <!-- Monthly Collection Chart -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Monthly Collections (<?php echo $filterYear; ?>)</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Month</th>
                                        <th>Amount</th>
                                        <th>Payments</th>
                                        <th>Avg</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $totalYearAmount = 0;
                                    $totalYearPayments = 0;
                                    foreach ($monthlyStats as $stat): 
                                        $totalYearAmount += $stat['total_amount'];
                                        $totalYearPayments += $stat['total_payments'];
                                    ?>
                                    <tr>
                                        <td><?php echo $months[$stat['month']]; ?></td>
                                        <td>₹<?php echo number_format($stat['total_amount'], 2); ?></td>
                                        <td><?php echo $stat['total_payments']; ?></td>
                                        <td>₹<?php echo $stat['total_payments'] > 0 ? number_format($stat['total_amount'] / $stat['total_payments'], 2) : '0.00'; ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                    <?php if (empty($monthlyStats)): ?>
                                    <tr>
                                        <td colspan="4" class="text-center">No collections found</td>
                                    </tr>
                                    <?php endif; ?>
                                </tbody>
                                <?php if (!empty($monthlyStats)): ?>
                                <tfoot>
                                    <tr class="table-primary">
                                        <th>Total</th>
                                        <th>₹<?php echo number_format($totalYearAmount, 2); ?></th>
                                        <th><?php echo $totalYearPayments; ?></th>
                                        <th>₹<?php echo $totalYearPayments > 0 ? number_format($totalYearAmount / $totalYearPayments, 2) : '0.00'; ?></th>
                                    </tr>
                                </tfoot>
                                <?php endif; ?>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Class-wise Collection -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Class-wise Collections 
                            (<?php echo $months[$filterMonth] . ' ' . $filterYear; ?>)
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Class</th>
                                        <th>Students</th>
                                        <th>Collected</th>
                                        <th>Payments</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    // Create a map of students by class
                                    $studentsMap = [];
                                    foreach ($studentsByClass as $classData) {
                                        $studentsMap[$classData['class']] = $classData['count'];
                                    }
                                    
                                    $totalClassAmount = 0;
                                    $totalClassPayments = 0;
                                    foreach ($classWiseStats as $stat): 
                                        $totalClassAmount += $stat['total_amount'];
                                        $totalClassPayments += $stat['total_payments'];
                                        $studentsInClass = isset($studentsMap[$stat['class']]) ? $studentsMap[$stat['class']] : 0;
                                    ?>
                                    <tr>
                                        <td><?php echo $stat['class']; ?></td>
                                        <td><?php echo $studentsInClass; ?></td>
                                        <td>₹<?php echo number_format($stat['total_amount'], 2); ?></td>
                                        <td><?php echo $stat['total_payments']; ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                    <?php if (empty($classWiseStats)): ?>
                                    <tr>
                                        <td colspan="4" class="text-center">No collections found</td>
                                    </tr>
                                    <?php endif; ?>
                                </tbody>
                                <?php if (!empty($classWiseStats)): ?>
                                <tfoot>
                                    <tr class="table-success">
                                        <th>Total</th>
                                        <th>-</th>
                                        <th>₹<?php echo number_format($totalClassAmount, 2); ?></th>
                                        <th><?php echo $totalClassPayments; ?></th>
                                    </tr>
                                </tfoot>
                                <?php endif; ?>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detailed Collections -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            Detailed Collections 
                            <?php if ($filterMonth && $filterYear): ?>
                            (<?php echo $months[$filterMonth] . ' ' . $filterYear; ?>)
                            <?php endif; ?>
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Student</th>
                                        <th>Class</th>
                                        <th>Month/Year</th>
                                        <th>Amount</th>
                                        <th>Mode</th>
                                        <th>Remarks</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($collections)): ?>
                                        <?php foreach ($collections as $collection): ?>
                                        <tr>
                                            <td><?php echo date('d/m/Y', strtotime($collection['payment_date'])); ?></td>
                                            <td>
                                                <?php echo htmlspecialchars($collection['name']); ?><br>
                                                <small class="text-muted"><?php echo htmlspecialchars($collection['roll_number']); ?></small>
                                            </td>
                                            <td><?php echo htmlspecialchars($collection['class']); ?></td>
                                            <td><?php echo $months[$collection['month']] . ' ' . $collection['year']; ?></td>
                                            <td>
                                                <span class="badge bg-success">₹<?php echo number_format($collection['amount_paid'], 2); ?></span>
                                            </td>
                                            <td>
                                                <span class="badge bg-<?php 
                                                    echo $collection['payment_mode'] == 'Cash' ? 'primary' : 
                                                        ($collection['payment_mode'] == 'Online' ? 'info' : 'secondary'); 
                                                ?>">
                                                    <?php echo $collection['payment_mode']; ?>
                                                </span>
                                            </td>
                                            <td><?php echo htmlspecialchars($collection['remarks']) ?: '-'; ?></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="7" class="text-center">No collections found for the selected criteria</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function printReport() {
            window.print();
        }
    </script>
    
    <style>
        @media print {
            .navbar, .btn, .card-header .btn {
                display: none !important;
            }
            
            .container {
                max-width: 100% !important;
            }
            
            .card {
                border: 1px solid #dee2e6 !important;
                page-break-inside: avoid;
            }
        }
    </style>
</body>
</html>
