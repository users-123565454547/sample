<?php
require_once '../includes/header.php';
requireRole('admin');

$conn = getDBConnection();
$users = $conn->query("SELECT * FROM users ORDER BY role, username")->fetch_all(MYSQLI_ASSOC);
$conn->close();
?>

<div class="bg-white rounded-lg shadow-md p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">User Management</h1>
        <button onclick="openAddUserModal()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
            <i class="fas fa-plus"></i> Add New User
        </button>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full">
            <thead>
                <tr class="bg-gray-50">
                    <th class="px-4 py-3 text-left">Username</th>
                    <th class="px-4 py-3 text-left">Full Name</th>
                    <th class="px-4 py-3 text-left">Email</th>
                    <th class="px-4 py-3 text-left">Role</th>
                    <th class="px-4 py-3 text-left">Status</th>
                    <th class="px-4 py-3 text-left">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                <tr class="border-t hover:bg-gray-50">
                    <td class="px-4 py-3"><?php echo htmlspecialchars($user['username']); ?></td>
                    <td class="px-4 py-3"><?php echo htmlspecialchars($user['full_name']); ?></td>
                    <td class="px-4 py-3"><?php echo htmlspecialchars($user['email']); ?></td>
                    <td class="px-4 py-3">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                            <?php echo $user['role'] == 'admin' ? 'bg-purple-100 text-purple-800' : 
                                   ($user['role'] == 'manager' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800'); ?>">
                            <?php echo ucfirst($user['role']); ?>
                        </span>
                    </td>
                    <td class="px-4 py-3">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                            <?php echo $user['is_active'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                            <?php echo $user['is_active'] ? 'Active' : 'Inactive'; ?>
                        </span>
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex space-x-2">
                            <button onclick="editUser(<?php echo $user['id']; ?>)" 
                                    class="text-blue-600 hover:text-blue-800">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button onclick="deleteUser(<?php echo $user['id']; ?>)" 
                                    class="text-red-600 hover:text-red-800">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Add/Edit User Modal -->
<div id="userModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 w-96">
        <h3 id="modalTitle" class="text-xl font-bold mb-4">Add New User</h3>
        <form id="userForm" method="POST" action="../api/save_user.php">
            <input type="hidden" id="user_id" name="user_id">
            
            <div class="space-y-4">
                <div>
                    <label class="block text-gray-700 mb-2" for="username">Username</label>
                    <input type="text" id="username" name="username" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div>
                    <label class="block text-gray-700 mb-2" for="password">Password</label>
                    <input type="password" id="password" name="password"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <p class="text-sm text-gray-500 mt-1">Leave blank to keep current password</p>
                </div>
                
                <div>
                    <label class="block text-gray-700 mb-2" for="full_name">Full Name</label>
                    <input type="text" id="full_name" name="full_name" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div>
                    <label class="block text-gray-700 mb-2" for="email">Email</label>
                    <input type="email" id="email" name="email"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div>
                    <label class="block text-gray-700 mb-2" for="role">Role</label>
                    <select id="role" name="role" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="cashier">Cashier</option>
                        <option value="manager">Manager</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                
                <div>
                    <label class="flex items-center">
                        <input type="checkbox" id="is_active" name="is_active" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500" checked>
                        <span class="ml-2 text-gray-700">Active</span>
                    </label>
                </div>
            </div>
            
            <div class="mt-6 flex space-x-3">
                <button type="submit" class="flex-1 bg-blue-600 text-white py-2 rounded-md hover:bg-blue-700 transition">
                    Save
                </button>
                <button type="button" onclick="closeUserModal()" class="flex-1 bg-gray-600 text-white py-2 rounded-md hover:bg-gray-700 transition">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openAddUserModal() {
    document.getElementById('modalTitle').textContent = 'Add New User';
    document.getElementById('userForm').reset();
    document.getElementById('user_id').value = '';
    document.getElementById('password').required = true;
    document.getElementById('userModal').classList.remove('hidden');
    document.getElementById('userModal').classList.add('flex');
}

function editUser(userId) {
    // In a real application, you would fetch user data via AJAX
    // For now, we'll redirect to an edit page
    window.location.href = 'edit_user.php?id=' + userId;
}

function deleteUser(userId) {
    if (confirm('Are you sure you want to delete this user?')) {
        window.location.href = '../api/delete_user.php?id=' + userId;
    }
}

function closeUserModal() {
    document.getElementById('userModal').classList.add('hidden');
    document.getElementById('userModal').classList.remove('flex');
}
</script>

<?php include '../includes/footer.php'; ?>