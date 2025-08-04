<?php
require_once __DIR__ . '/controllers/Controllers.php';

$studentController = new StudentController();
$feeController = new FeeController();
$message = '';
$messageType = '';

// Handle fee collection form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['collect_fee'])) {
    $result = $feeController->collectFee(
        $_POST['student_id'],
        $_POST['amount_paid'],
        $_POST['month'],
        $_POST['year'],
        $_POST['payment_date'],
        $_POST['payment_mode'],
        $_POST['remarks']
    );
    
    if (is_numeric($result)) {
        $message = 'Fee collected successfully!';
        $messageType = 'success';
    } else {
        $message = $result;
        $messageType = 'danger';
    }
}

// Get selected student if provided
$selectedStudent = null;
if (isset($_GET['student_id'])) {
    $selectedStudent = $studentController->getStudentById($_GET['student_id']);
}

// Search students
$searchTerm = isset($_GET['search']) ? $_GET['search'] : '';
if ($searchTerm) {
    $students = $studentController->searchStudents($searchTerm);
} else {
    $students = $studentController->getAllStudents();
}

$months = FeeCollection::getMonths();
$paymentModes = FeeCollection::getPaymentModes();
$classes = Student::getClasses();

// Get recent fee collections
$recentCollections = $feeController->getAllFeeCollections();
$recentCollections = array_slice($recentCollections, 0, 10); // Get last 10
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fee Collection - School Fee Collection System</title>
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
                <a class="nav-link active" href="fee_collection.php">Fee Collection</a>
                <a class="nav-link" href="reports.php">Reports</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <?php if ($message): ?>
        <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show" role="alert">
            <?php echo $message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-12">
                <h1 class="mb-4">Fee Collection</h1>
            </div>
        </div>

        <div class="row">
            <!-- Fee Collection Form -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Collect Fee</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="mb-3">
                                <label for="student_search" class="form-label">Search Student</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="student_search" placeholder="Search by name, roll number, or mobile">
                                    <button type="button" class="btn btn-outline-primary" onclick="searchStudent()">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                                <div id="student_results" class="mt-2"></div>
                            </div>

                            <div class="mb-3">
                                <label for="student_id" class="form-label">Selected Student *</label>
                                <input type="hidden" name="student_id" id="student_id" value="<?php echo $selectedStudent ? $selectedStudent['id'] : ''; ?>" required>
                                <input type="text" class="form-control" id="student_display" value="<?php echo $selectedStudent ? $selectedStudent['name'] . ' (' . $selectedStudent['roll_number'] . ')' : ''; ?>" readonly placeholder="Select a student">
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="month" class="form-label">Month *</label>
                                        <select class="form-control" name="month" required>
                                            <option value="">Select Month</option>
                                            <?php foreach ($months as $num => $name): ?>
                                            <option value="<?php echo $num; ?>" <?php echo (date('n') == $num) ? 'selected' : ''; ?>>
                                                <?php echo $name; ?>
                                            </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="year" class="form-label">Year *</label>
                                        <input type="number" class="form-control" name="year" value="<?php echo date('Y'); ?>" min="2020" max="2030" required>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="amount_paid" class="form-label">Amount Paid *</label>
                                <div class="input-group">
                                    <span class="input-group-text">₹</span>
                                    <input type="number" step="0.01" class="form-control" name="amount_paid" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="payment_date" class="form-label">Payment Date *</label>
                                <input type="date" class="form-control" name="payment_date" value="<?php echo date('Y-m-d'); ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="payment_mode" class="form-label">Payment Mode *</label>
                                <select class="form-control" name="payment_mode" required>
                                    <option value="">Select Payment Mode</option>
                                    <?php foreach ($paymentModes as $mode): ?>
                                    <option value="<?php echo $mode; ?>"><?php echo $mode; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="remarks" class="form-label">Remarks</label>
                                <textarea class="form-control" name="remarks" rows="3"></textarea>
                            </div>

                            <button type="submit" name="collect_fee" class="btn btn-success">
                                <i class="fas fa-money-bill me-1"></i>Collect Fee
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Recent Collections -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Recent Collections</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Student</th>
                                        <th>Month</th>
                                        <th>Amount</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($recentCollections)): ?>
                                        <?php foreach ($recentCollections as $collection): ?>
                                        <tr>
                                            <td>
                                                <small>
                                                    <?php echo htmlspecialchars($collection['name']); ?><br>
                                                    <span class="text-muted"><?php echo htmlspecialchars($collection['roll_number']); ?></span>
                                                </small>
                                            </td>
                                            <td><?php echo $months[$collection['month']]; ?> <?php echo $collection['year']; ?></td>
                                            <td>₹<?php echo number_format($collection['amount_paid'], 2); ?></td>
                                            <td><?php echo date('d/m/Y', strtotime($collection['payment_date'])); ?></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="4" class="text-center">No collections yet</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- All Students Table -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">All Students</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <div class="row">
                                <div class="col-md-6">
                                    <form method="GET" class="d-flex">
                                        <input type="text" name="search" class="form-control me-2" placeholder="Search students..." value="<?php echo htmlspecialchars($searchTerm); ?>">
                                        <button type="submit" class="btn btn-outline-primary">
                                            <i class="fas fa-search"></i>
                                        </button>
                                        <?php if ($searchTerm): ?>
                                        <a href="fee_collection.php" class="btn btn-outline-secondary ms-2">
                                            <i class="fas fa-times"></i>
                                        </a>
                                        <?php endif; ?>
                                    </form>
                                </div>
                            </div>
                        </div>
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
                                    <?php foreach ($students as $student): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($student['roll_number']); ?></td>
                                        <td><?php echo htmlspecialchars($student['name']); ?></td>
                                        <td><?php echo htmlspecialchars($student['class']); ?></td>
                                        <td><?php echo htmlspecialchars($student['parent_mobile']); ?></td>
                                        <td>
                                            <button class="btn btn-sm btn-primary" onclick="selectStudent(<?php echo htmlspecialchars(json_encode($student)); ?>)">
                                                <i class="fas fa-check"></i> Select
                                            </button>
                                            <a href="student_fees.php?id=<?php echo $student['id']; ?>" class="btn btn-sm btn-info">
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
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function selectStudent(student) {
            document.getElementById('student_id').value = student.id;
            document.getElementById('student_display').value = student.name + ' (' + student.roll_number + ')';
            
            // Scroll to form
            document.querySelector('.card').scrollIntoView({ behavior: 'smooth' });
        }

        function searchStudent() {
            const searchTerm = document.getElementById('student_search').value;
            if (searchTerm.length >= 2) {
                // In a real implementation, this would be an AJAX call
                window.location.href = 'fee_collection.php?search=' + encodeURIComponent(searchTerm);
            }
        }

        // Allow Enter key to trigger search
        document.getElementById('student_search').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                searchStudent();
            }
        });
    </script>
</body>
</html>
