<?php 
    require_once '../Database/Database.php';
    require_once '../Models/User.php';
    include '../layout/header.php'; 


    $db = new Database();
    $conn = $db->getConnection();

    User::setConnection($conn);

    $currentUser = User::findEmail($_SESSION['email']);

    $users = User::all();
        
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
    
    if ($currentUser->role === 'student' || $currentUser->role === 'staff' || $currentUser->role === 'others') {
        header("Location: profile.php?id={$currentUser->id}");
        exit();
    }
    if ($_SESSION['role'] !== 'superadmin' && $_SESSION['role'] !== 'admin') {
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

?>

<div class="d-flex justify-content-center mt-5">
    <h3 class="mb-0">User Management</h3>
</div>
<div class="container py-5">
    <div class="card shadow-lg rounded-4 border-0">
        <div class="card-body">
            <a class="btn btn-primary mb-2 mt-2" href="create.php">Create New User</a>
            <table id="usersTable" class="table table-striped table-hover text-center align-middle">
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
                    $i = 1;
                    foreach ($users as $user):
                        if ($user->role === 'student' || $user->role === 'staff' || $user->role === 'others'):
                    ?>
                            <tr>
                                <td class="text-center"><?= $i++ ?></td>
                                <td class="text-center"><?= $user->name ?></td>
                                <td class="text-center"><?= $user->email ?></td>
                                <td class="text-center"><?= $user->phone_number ?></td>
                                <td class="text-center"><?= ucfirst($user->role) ?></td>
                                <td>
                                    <span class="badge <?= $user->account_status === 'active' ? 'bg-success' : 'bg-danger' ?>">
                                        <?= ucfirst($user->account_status) ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <?php if ($currentUser->role == 'admin' || $currentUser->role == 'superadmin'):?> 
                                        <a href='show.php?id=<?=$user->id?>' class='btn btn-info'>                                                
                                            <svg xmlns="http://www.w3.org/2000/svg" height="20" width="22.5" viewBox="0 0 576 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path d="M288 32c-80.8 0-145.5 36.8-192.6 80.6C48.6 156 17.3 208 2.5 243.7c-3.3 7.9-3.3 16.7 0 24.6C17.3 304 48.6 356 95.4 399.4C142.5 443.2 207.2 480 288 480s145.5-36.8 192.6-80.6c46.8-43.5 78.1-95.4 93-131.1c3.3-7.9 3.3-16.7 0-24.6c-14.9-35.7-46.2-87.7-93-131.1C433.5 68.8 368.8 32 288 32zM144 256a144 144 0 1 1 288 0 144 144 0 1 1 -288 0zm144-64c0 35.3-28.7 64-64 64c-7.1 0-13.9-1.2-20.3-3.3c-5.5-1.8-11.9 1.6-11.7 7.4c.3 6.9 1.3 13.8 3.2 20.7c13.7 51.2 66.4 81.6 117.6 67.9s81.6-66.4 67.9-117.6c-11.1-41.5-47.8-69.4-88.6-71.1c-5.8-.2-9.2 6.1-7.4 11.7c2.1 6.4 3.3 13.2 3.3 20.3z"/></svg>
                                        </a> 
                                        
                                    <?php endif; ?>

                                    <a href="edit.php?id=<?= $user->id ?>" class="btn btn-primary ">
                                        <svg xmlns="http://www.w3.org/2000/svg" height="20" width="25" viewBox="0 0 640 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path d="M224 256A128 128 0 1 0 224 0a128 128 0 1 0 0 256zm-45.7 48C79.8 304 0 383.8 0 482.3C0 498.7 13.3 512 29.7 512l293.1 0c-3.1-8.8-3.7-18.4-1.4-27.8l15-60.1c2.8-11.3 8.6-21.5 16.8-29.7l40.3-40.3c-32.1-31-75.7-50.1-123.9-50.1l-91.4 0zm435.5-68.3c-15.6-15.6-40.9-15.6-56.6 0l-29.4 29.4 71 71 29.4-29.4c15.6-15.6 15.6-40.9 0-56.6l-14.4-14.4zM375.9 417c-4.1 4.1-7 9.2-8.4 14.9l-15 60.1c-1.4 5.5 .2 11.2 4.2 15.2s9.7 5.6 15.2 4.2l60.1-15c5.6-1.4 10.8-4.3 14.9-8.4L576.1 358.7l-71-71L375.9 417z"/></svg>                                            
                                    </a>
                                    
                                    <a href="destroy.php?id=<?=$user->id?>" class="btn btn-danger">
                                                <svg xmlns="http://www.w3.org/2000/svg" height="20" width="25" viewBox="0 0 640 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path d="M38.8 5.1C28.4-3.1 13.3-1.2 5.1 9.2S-1.2 34.7 9.2 42.9l592 464c10.4 8.2 25.5 6.3 33.7-4.1s6.3-25.5-4.1-33.7L353.3 251.6C407.9 237 448 187.2 448 128C448 57.3 390.7 0 320 0C250.2 0 193.5 55.8 192 125.2L38.8 5.1zM264.3 304.3C170.5 309.4 96 387.2 96 482.3c0 16.4 13.3 29.7 29.7 29.7l388.6 0c3.9 0 7.6-.7 11-2.1l-261-205.6z"/></svg>                                            
                                            </a>

                                    <?php if ($currentUser->role == 'admin' || $currentUser->role == 'superadmin'):?>
                                        <?php if ($user->account_status == 'inactive'): ?>
                                            <a href="update.php?status=reactivate&id=<?= $user->id?>" class="btn btn-success w-50">Reactivate</a>
                                        <?php else: ?>
                                            <a href="update.php?status=deactivate&id=<?= $user->id ?>" class="btn btn-danger w-50">Deactivate</a>
                                        <?php endif; ?>
                                    <?php endif;?>
                                </td>
                            </tr>
                    <?php
                        endif;
                    endforeach;
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include 'datatables.php'; ?>
<?php include '../layout/footer.php'; ?>
