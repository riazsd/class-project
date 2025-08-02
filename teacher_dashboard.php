<?php
require_once 'classes/Auth.php';
require_once 'classes/Course.php';

Auth::requireRole('teacher');
$user = Auth::getUser();
$course = new Course();

// Handle status updates
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_status'])) {
        $registration_id = $_POST['registration_id'];
        $new_status = $_POST['status'];

        if (in_array($new_status, ['approved', 'suggested', 'pending'])) {
            if ($course->updateRegistrationStatus($registration_id, $new_status)) {
                $success = 'Registration status updated successfully!';
            } else {
                $error = 'Failed to update registration status.';
            }
        }
    } elseif (isset($_POST['remove_registration'])) {
        $registration_id = $_POST['registration_id'];

        if ($course->removeRegistration($registration_id)) {
            $success = 'Registration removed successfully!';
        } else {
            $error = 'Failed to remove registration.';
        }
    }
}

$registrations = $course->getTeacherCourseRegistrations($user['id']);

// Group registrations by course
$grouped_registrations = [];
foreach ($registrations as $registration) {
    $course_key = $registration['course_code'];
    if (!isset($grouped_registrations[$course_key])) {
        $grouped_registrations[$course_key] = [
            'course_name' => $registration['course_name'],
            'registrations' => []
        ];
    }
    $grouped_registrations[$course_key]['registrations'][] = $registration;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Dashboard - Class Project</title>
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
                <h1 class="text-2xl font-bold text-gray-800">Teacher Dashboard</h1>
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

        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="bg-gradient-to-r from-purple-600 to-indigo-600 px-6 py-4">
                <h2 class="text-xl font-bold text-white">Student Course Registrations</h2>
                <p class="text-purple-100 text-sm">Manage student registrations for your courses</p>
            </div>

            <div class="p-6">
                <?php if (empty($grouped_registrations)): ?>
                    <div class="text-center py-12">
                        <div class="text-gray-400 text-6xl mb-4">📚</div>
                        <p class="text-gray-500 text-lg">No student registrations yet</p>
                        <p class="text-gray-400 text-sm">Students will appear here when they register for your courses</p>
                    </div>
                <?php else: ?>
                    <div class="space-y-8">
                        <?php foreach ($grouped_registrations as $course_code => $course_data): ?>
                            <div class="border border-gray-200 rounded-lg overflow-hidden">
                                <div class="bg-gray-50 px-4 py-3 border-b border-gray-200">
                                    <h3 class="font-semibold text-lg text-gray-800">
                                        <?php echo htmlspecialchars($course_code); ?> - <?php echo htmlspecialchars($course_data['course_name']); ?>
                                    </h3>
                                    <p class="text-gray-600 text-sm"><?php echo count($course_data['registrations']); ?> student(s) registered</p>
                                </div>

                                <div class="divide-y divide-gray-200">
                                    <?php foreach ($course_data['registrations'] as $registration): ?>
                                        <div class="p-4 hover:bg-gray-50 transition duration-200">
                                            <div class="flex justify-between items-center">
                                                <div class="flex-1">
                                                    <h4 class="font-medium text-gray-800">
                                                        <?php echo htmlspecialchars($registration['student_first_name'] . ' ' . $registration['student_last_name']); ?>
                                                    </h4>
                                                    <p class="text-gray-600 text-sm"><?php echo htmlspecialchars($registration['student_email']); ?></p>
                                                    <p class="text-gray-400 text-xs mt-1">
                                                        Registered: <?php echo date('M j, Y \a\t g:i A', strtotime($registration['registered_at'])); ?>
                                                    </p>
                                                    <?php if ($registration['updated_at'] !== $registration['registered_at']): ?>
                                                        <p class="text-gray-400 text-xs">
                                                            Updated: <?php echo date('M j, Y \a\t g:i A', strtotime($registration['updated_at'])); ?>
                                                        </p>
                                                    <?php endif; ?>
                                                </div>

                                                <div class="ml-4 flex items-center space-x-3">
                                                    <!-- Current Status -->
                                                    <?php
                                                    $status_colors = [
                                                        'pending' => 'bg-yellow-100 text-yellow-800',
                                                        'approved' => 'bg-green-100 text-green-800',
                                                        'suggested' => 'bg-blue-100 text-blue-800'
                                                    ];
                                                    $status_color = $status_colors[$registration['status']] ?? 'bg-gray-100 text-gray-800';
                                                    ?>
                                                    <span class="px-3 py-1 rounded-full text-sm font-medium <?php echo $status_color; ?>">
                                                        <?php echo ucfirst($registration['status']); ?>
                                                    </span>

                                                    <!-- Action Buttons -->
                                                    <div class="flex space-x-2">
                                                        <?php if ($registration['status'] !== 'approved'): ?>
                                                            <form method="POST" class="inline">
                                                                <input type="hidden" name="registration_id" value="<?php echo $registration['id']; ?>">
                                                                <input type="hidden" name="status" value="approved">
                                                                <button type="submit" name="update_status"
                                                                        class="bg-green-600 text-white px-3 py-1 text-sm rounded hover:bg-green-700 transition duration-200"
                                                                        title="Approve Registration">
                                                                    ✓ Approve
                                                                </button>
                                                            </form>
                                                        <?php endif; ?>

                                                        <?php if ($registration['status'] !== 'suggested'): ?>
                                                            <form method="POST" class="inline">
                                                                <input type="hidden" name="registration_id" value="<?php echo $registration['id']; ?>">
                                                                <input type="hidden" name="status" value="suggested">
                                                                <button type="submit" name="update_status"
                                                                        class="bg-blue-600 text-white px-3 py-1 text-sm rounded hover:bg-blue-700 transition duration-200"
                                                                        title="Mark as Suggested">
                                                                    ⭐ Suggest
                                                                </button>
                                                            </form>
                                                        <?php endif; ?>

                                                        <?php if ($registration['status'] !== 'pending'): ?>
                                                            <form method="POST" class="inline">
                                                                <input type="hidden" name="registration_id" value="<?php echo $registration['id']; ?>">
                                                                <input type="hidden" name="status" value="pending">
                                                                <button type="submit" name="update_status"
                                                                        class="bg-yellow-600 text-white px-3 py-1 text-sm rounded hover:bg-yellow-700 transition duration-200"
                                                                        title="Mark as Pending">
                                                                    ⏳ Pending
                                                                </button>
                                                            </form>
                                                        <?php endif; ?>

                                                        <form method="POST" class="inline" onsubmit="return confirm('Are you sure you want to remove this registration?')">
                                                            <input type="hidden" name="registration_id" value="<?php echo $registration['id']; ?>">
                                                            <button type="submit" name="remove_registration"
                                                                    class="bg-red-600 text-white px-3 py-1 text-sm rounded hover:bg-red-700 transition duration-200"
                                                                    title="Remove Registration">
                                                                ✗ Remove
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Legend -->
        <div class="mt-8 bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="bg-gray-100 px-6 py-3">
                <h3 class="font-semibold text-gray-800">Status Legend</h3>
            </div>
            <div class="p-6">
                <div class="grid md:grid-cols-3 gap-4">
                    <div class="flex items-center space-x-3">
                        <span class="px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">Pending</span>
                        <span class="text-gray-600 text-sm">Awaiting your review</span>
                    </div>
                    <div class="flex items-center space-x-3">
                        <span class="px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">Approved</span>
                        <span class="text-gray-600 text-sm">Registration confirmed</span>
                    </div>
                    <div class="flex items-center space-x-3">
                        <span class="px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">Suggested</span>
                        <span class="text-gray-600 text-sm">Recommended for student</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
