<?php
require_once 'classes/Auth.php';
require_once 'classes/Course.php';

Auth::requireRole('student');
$user = Auth::getUser();
$course = new Course();

// Handle course registration
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register_course'])) {
    $course_id = $_POST['course_id'];
    if ($course->registerForCourse($user['id'], $course_id)) {
        $success = 'Successfully registered for the course!';
    } else {
        $error = 'Registration failed. You may already be registered for this course.';
    }
}

$available_courses = $course->getAvailableCoursesForStudent($user['id']);
$my_registrations = $course->getStudentRegistrations($user['id']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard - Class Project</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#3b82f6',
                        secondary: '#1e40af'
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Navigation -->
    <nav class="bg-white shadow-lg">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <h1 class="text-2xl font-bold text-gray-800">Student Dashboard</h1>
                <div class="flex items-center space-x-4">
                    <span class="text-gray-600">Welcome, <?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></span>
                    <a href="logout.php" class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 transition duration-200">Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="container mx-auto px-4 py-8">
        <?php if (isset($success)): ?>
            <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
                <?php echo htmlspecialchars($success); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($error)): ?>
            <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <div class="grid lg:grid-cols-2 gap-8">
            <!-- Available Courses -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-4">
                    <h2 class="text-xl font-bold text-white">Available Courses</h2>
                </div>
                <div class="p-6">
                    <?php if (empty($available_courses)): ?>
                        <p class="text-gray-500 text-center py-8">No courses available at the moment.</p>
                    <?php else: ?>
                        <div class="space-y-4">
                            <?php foreach ($available_courses as $course_item): ?>
                                <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition duration-200">
                                    <div class="flex justify-between items-start">
                                        <div class="flex-1">
                                            <h3 class="font-semibold text-lg text-gray-800"><?php echo htmlspecialchars($course_item['course_code']); ?></h3>
                                            <h4 class="text-gray-600 mb-2"><?php echo htmlspecialchars($course_item['course_name']); ?></h4>
                                            <p class="text-gray-500 text-sm mb-2"><?php echo htmlspecialchars($course_item['description']); ?></p>
                                            <p class="text-gray-500 text-sm">
                                                Instructor: <?php echo htmlspecialchars($course_item['first_name'] . ' ' . $course_item['last_name']); ?>
                                            </p>
                                        </div>
                                        <div class="ml-4">
                                            <?php if ($course_item['registration_status']): ?>
                                                <?php
                                                $status_colors = [
                                                    'pending' => 'bg-yellow-100 text-yellow-800',
                                                    'approved' => 'bg-green-100 text-green-800',
                                                    'suggested' => 'bg-blue-100 text-blue-800',
                                                    'removed' => 'bg-red-100 text-red-800'
                                                ];
                                                $status_color = isset($status_colors[$course_item['registration_status']]) ? $status_colors[$course_item['registration_status']] : 'bg-gray-100 text-gray-800';
                                                ?>
                                                <span class="px-3 py-1 rounded-full text-sm font-medium <?php echo $status_color; ?>">
                                                    <?php echo ucfirst($course_item['registration_status']); ?>
                                                </span>
                                            <?php else: ?>
                                                <form method="POST" class="inline">
                                                    <input type="hidden" name="course_id" value="<?php echo $course_item['id']; ?>">
                                                    <button type="submit" name="register_course"
                                                            class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-200">
                                                        Register
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- My Registrations -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <div class="bg-gradient-to-r from-green-600 to-emerald-600 px-6 py-4">
                    <h2 class="text-xl font-bold text-white">My Course Registrations</h2>
                </div>
                <div class="p-6">
                    <?php if (empty($my_registrations)): ?>
                        <p class="text-gray-500 text-center py-8">You haven't registered for any courses yet.</p>
                    <?php else: ?>
                        <div class="space-y-4">
                            <?php foreach ($my_registrations as $registration): ?>
                                <div class="border border-gray-200 rounded-lg p-4">
                                    <div class="flex justify-between items-start">
                                        <div class="flex-1">
                                            <h3 class="font-semibold text-lg text-gray-800"><?php echo htmlspecialchars($registration['course_code']); ?></h3>
                                            <h4 class="text-gray-600 mb-2"><?php echo htmlspecialchars($registration['course_name']); ?></h4>
                                            <p class="text-gray-500 text-sm mb-1">
                                                Instructor: <?php echo htmlspecialchars($registration['teacher_first_name'] . ' ' . $registration['teacher_last_name']); ?>
                                            </p>
                                            <p class="text-gray-400 text-xs">
                                                Registered: <?php echo date('M j, Y', strtotime($registration['registered_at'])); ?>
                                            </p>
                                        </div>
                                        <div class="ml-4">
                                            <?php
                                            $status_colors = [
                                                'pending' => 'bg-yellow-100 text-yellow-800',
                                                'approved' => 'bg-green-100 text-green-800',
                                                'suggested' => 'bg-blue-100 text-blue-800',
                                                'removed' => 'bg-red-100 text-red-800'
                                            ];
                                            $status_color = isset($status_colors[$registration['status']]) ? $status_colors[$registration['status']] : 'bg-gray-100 text-gray-800';
                                            ?>
                                            <span class="px-3 py-1 rounded-full text-sm font-medium <?php echo $status_color; ?>">
                                                <?php echo ucfirst($registration['status']); ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
