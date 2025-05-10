<?php
require_once '../database/Database.php';
require_once '../models/User.php';
include '../layout/header.php'; 
$database = new Database();
$db = $database->getConnection();
User::setConnection($db);

if (!isset($_SESSION['email'])) {
    http_response_code(404);
    echo '<script>
    Swal.fire({
        title: "Error!",
        text: "Login first!",
        icon: "error",
        confirmButtonText: "Ok"
    }).then(function() {
        window.location.href = "../auth/login.php";
    });
    </script>';
    exit();
}

if ($_SESSION['role'] !== 'superadmin') {
    http_response_code(403);
    echo '<script>
    Swal.fire({
        title: "Error!",
        text: "No user access.",
        icon: "error",
        confirmButtonText: "Ok"
    }).then(function() {
        window.location.href = "../index.php";
    });
    </script>';
    exit();
}

$users = User::all();

?>
    <div class="d-flex justify-content-center mt-5">
        <h3 class="mb-0">Admin Management</h3>
    </div>
    <div class="container py-5">
        <div class="card shadow-lg rounded-4 border-0">
            <div class="card-body">
                <a class="btn btn-primary mb-2 mt-2" href="create.php"> Add New Admin </a>
                <div class="d-flex align-items-end mb-3">
                    <a href="../reports/users_report.php?role=admin" target="_blank" class="btn btn-secondary me-2">View All Registered Admins</a>
                </div>
                <table id="adminsTable" class="table table-striped table-hover text-center align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th class="text-center">#</th>
                            <th class="text-center">Name</th>
                            <th class="text-center">Email</th>
                            <th class="text-center">Phone Number</th>
                            <th class="text-center">Role</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            $i=1; 
                            if ($users) :
                            foreach ($users as $user) :
                                if ($user->role === 'superadmin' || $user->role === 'admin') : 
                        ?>
                                    <tr>
                                        <td class="text-center"><?= $i++ ?></td>
                                        <td class="text-center"><?= $user->name ?></td>
                                        <td class="text-center"><?= $user->email ?></td>
                                        <td class="text-center"><?= $user->phone_number ?></td>
                                        <td class="text-center"><?= $user->role ?></td>
                                        <td class="text-center">
                                            <span class="badge <?= $user->account_status === 'active' ? 'bg-success' : 'bg-danger' ?>">
                                                <?= ucfirst($user->account_status) ?>
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <a href="edit.php?id=<?= $user->id ?>" class="btn btn-primary ">
                                                <svg xmlns="http://www.w3.org/2000/svg" height="20" width="25" viewBox="0 0 640 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path d="M224 256A128 128 0 1 0 224 0a128 128 0 1 0 0 256zm-45.7 48C79.8 304 0 383.8 0 482.3C0 498.7 13.3 512 29.7 512l293.1 0c-3.1-8.8-3.7-18.4-1.4-27.8l15-60.1c2.8-11.3 8.6-21.5 16.8-29.7l40.3-40.3c-32.1-31-75.7-50.1-123.9-50.1l-91.4 0zm435.5-68.3c-15.6-15.6-40.9-15.6-56.6 0l-29.4 29.4 71 71 29.4-29.4c15.6-15.6 15.6-40.9 0-56.6l-14.4-14.4zM375.9 417c-4.1 4.1-7 9.2-8.4 14.9l-15 60.1c-1.4 5.5 .2 11.2 4.2 15.2s9.7 5.6 15.2 4.2l60.1-15c5.6-1.4 10.8-4.3 14.9-8.4L576.1 358.7l-71-71L375.9 417z"/></svg>                                            
                                            </a>

                                            <a href="destroy.php?id=<?=$user->id?>" class="btn btn-danger">
                                                <svg xmlns="http://www.w3.org/2000/svg" height="20" width="25" viewBox="0 0 640 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path d="M38.8 5.1C28.4-3.1 13.3-1.2 5.1 9.2S-1.2 34.7 9.2 42.9l592 464c10.4 8.2 25.5 6.3 33.7-4.1s6.3-25.5-4.1-33.7L353.3 251.6C407.9 237 448 187.2 448 128C448 57.3 390.7 0 320 0C250.2 0 193.5 55.8 192 125.2L38.8 5.1zM264.3 304.3C170.5 309.4 96 387.2 96 482.3c0 16.4 13.3 29.7 29.7 29.7l388.6 0c3.9 0 7.6-.7 11-2.1l-261-205.6z"/></svg>                                            
                                            </a>

                                            <a href="../reports/admin_profile.php?id=<?= $user->id ?>" target="_blank" class="btn btn-primary">
                                                <svg xmlns="http://www.w3.org/2000/svg" height="20" width="22.5" viewBox="0 0 576 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path d="M0 64C0 28.7 28.7 0 64 0L224 0l0 128c0 17.7 14.3 32 32 32l128 0 0 128-168 0c-13.3 0-24 10.7-24 24s10.7 24 24 24l168 0 0 112c0 35.3-28.7 64-64 64L64 512c-35.3 0-64-28.7-64-64L0 64zM384 336l0-48 110.1 0-39-39c-9.4-9.4-9.4-24.6 0-33.9s24.6-9.4 33.9 0l80 80c9.4 9.4 9.4 24.6 0 33.9l-80 80c-9.4 9.4-24.6 9.4-33.9 0s-9.4-24.6 0-33.9l39-39L384 336zm0-208l-128 0L256 0 384 128z"/></svg>
                                            </a> 
                                
                                            <?php if ($user->account_status == 'inactive'): ?>
                                                <a href="update.php?status=reactivate&id=<?= $user->id?>" class="btn btn-success w-50">Reactivate</a>
                                            <?php else: ?>
                                                <a href="update.php?status=deactivate&id=<?= $user->id ?>" class="btn btn-danger w-50">Deactivate</a>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endif;
                            endforeach;?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php   include 'datatables.php';
        include '../layout/footer.php';
        