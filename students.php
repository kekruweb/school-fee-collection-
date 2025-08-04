<?php
require_once __DIR__ . '/controllers/Controllers.php';

$studentController = new StudentController();
$message = '';
$messageType = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add':
                $result = $studentController->addStudent(
                    $_POST['name'],
                    $_POST['roll_number'],
                    $_POST['class'],
                    $_POST['parent_mobile']
                );
                if (is_numeric($result)) {
                    $message = 'Student added successfully!';
                    $messageType = 'success';
                } else {
                    $message = $result;
                    $messageType = 'danger';
                }
                break;
                
            case 'edit':
                $result = $studentController->updateStudent(
                    $_POST['student_id'],
                    $_POST['name'],
                    $_POST['roll_number'],
                    $_POST['class'],
                    $_POST['parent_mobile']
                );
                if ($result) {
                    $message = 'Student updated successfully!';
                    $messageType = 'success';
                } else {
                    $message = 'Failed to update student.';
                    $messageType = 'danger';
                }
                break;
                
            case 'delete':
                $result = $studentController->deleteStudent($_POST['student_id']);
                if ($result) {
                    $message = 'Student deleted successfully!';
                    $messageType = 'success';
                } else {
                    $message = 'Failed to delete student.';
                    $messageType = 'danger';
                }
                break;
        }
    }
}

// Get students (with search if provided)
$searchTerm = isset($_GET['search']) ? $_GET['search'] : '';
if ($searchTerm) {
    $students = $studentController->searchStudents($searchTerm);
} else {
    $students = $studentController->getAllStudents();
}

$classes = Student::getClasses();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Students - School Fee Collection System</title>
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
                <a class="nav-link active" href="students.php">Students</a>
                <a class="nav-link" href="fee_collection.php">Fee Collection</a>
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

        <div class="row mb-4">
            <div class="col-md-6">
                <h1>Students Management</h1>
            </div>
            <div class="col-md-6 text-end">
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addStudentModal">
                    <i class="fas fa-plus me-1"></i>Add Student
                </button>
            </div>
        </div>

        <!-- Search Form -->
        <div class="row mb-4">
            <div class="col-md-6">
                <form method="GET" class="d-flex">
                    <input type="text" name="search" class="form-control me-2" placeholder="Search by name, roll number, or mobile" value="<?php echo htmlspecialchars($searchTerm); ?>">
                    <button type="submit" class="btn btn-outline-primary">
                        <i class="fas fa-search"></i>
                    </button>
                    <?php if ($searchTerm): ?>
                    <a href="students.php" class="btn btn-outline-secondary ms-2">
                        <i class="fas fa-times"></i>
                    </a>
                    <?php endif; ?>
                </form>
            </div>
        </div>

        <!-- Students Table -->
        <div class="card">
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
                            <?php if (!empty($students)): ?>
                                <?php foreach ($students as $student): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($student['roll_number']); ?></td>
                                    <td><?php echo htmlspecialchars($student['name']); ?></td>
                                    <td><?php echo htmlspecialchars($student['class']); ?></td>
                                    <td><?php echo htmlspecialchars($student['parent_mobile']); ?></td>
                                    <td>
                                        <button class="btn btn-sm btn-primary" onclick="editStudent(<?php echo htmlspecialchars(json_encode($student)); ?>)">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <a href="fee_collection.php?student_id=<?php echo $student['id']; ?>" class="btn btn-sm btn-success">
                                            <i class="fas fa-money-bill"></i>
                                        </a>
                                        <a href="student_fees.php?id=<?php echo $student['id']; ?>" class="btn btn-sm btn-info">
                                            <i class="fas fa-history"></i>
                                        </a>
                                        <button class="btn btn-sm btn-danger" onclick="deleteStudent(<?php echo $student['id']; ?>, '<?php echo htmlspecialchars($student['name']); ?>')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center">No students found</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Student Modal -->
    <div class="modal fade" id="addStudentModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title">Add Student</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="action" value="add">
                        <div class="mb-3">
                            <label for="name" class="form-label">Name *</label>
                            <input type="text" class="form-control" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="roll_number" class="form-label">Roll Number *</label>
                            <input type="text" class="form-control" name="roll_number" required>
                        </div>
                        <div class="mb-3">
                            <label for="class" class="form-label">Class *</label>
                            <select class="form-control" name="class" required>
                                <option value="">Select Class</option>
                                <?php foreach ($classes as $class): ?>
                                <option value="<?php echo $class; ?>"><?php echo $class; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="parent_mobile" class="form-label">Parent Mobile *</label>
                            <input type="text" class="form-control" name="parent_mobile" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add Student</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Student Modal -->
    <div class="modal fade" id="editStudentModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Student</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="action" value="edit">
                        <input type="hidden" name="student_id" id="edit_student_id">
                        <div class="mb-3">
                            <label for="edit_name" class="form-label">Name *</label>
                            <input type="text" class="form-control" name="name" id="edit_name" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_roll_number" class="form-label">Roll Number *</label>
                            <input type="text" class="form-control" name="roll_number" id="edit_roll_number" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_class" class="form-label">Class *</label>
                            <select class="form-control" name="class" id="edit_class" required>
                                <option value="">Select Class</option>
                                <?php foreach ($classes as $class): ?>
                                <option value="<?php echo $class; ?>"><?php echo $class; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="edit_parent_mobile" class="form-label">Parent Mobile *</label>
                            <input type="text" class="form-control" name="parent_mobile" id="edit_parent_mobile" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Student</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Student Modal -->
    <div class="modal fade" id="deleteStudentModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title">Delete Student</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="student_id" id="delete_student_id">
                        <p>Are you sure you want to delete student <strong id="delete_student_name"></strong>?</p>
                        <p class="text-danger"><small>This will also delete all fee records for this student.</small></p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Delete Student</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function editStudent(student) {
            document.getElementById('edit_student_id').value = student.id;
            document.getElementById('edit_name').value = student.name;
            document.getElementById('edit_roll_number').value = student.roll_number;
            document.getElementById('edit_class').value = student.class;
            document.getElementById('edit_parent_mobile').value = student.parent_mobile;
            
            var modal = new bootstrap.Modal(document.getElementById('editStudentModal'));
            modal.show();
        }

        function deleteStudent(id, name) {
            document.getElementById('delete_student_id').value = id;
            document.getElementById('delete_student_name').textContent = name;
            
            var modal = new bootstrap.Modal(document.getElementById('deleteStudentModal'));
            modal.show();
        }
    </script>
</body>
</html>
