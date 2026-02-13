<?php
require_once("admin_check.php");
require_once("../includes/db.php");

if(!isset($_GET['id']) || !isset($_GET['action'])){
    header("Location: organizer_requests.php");
    exit;
}

$org_id = (int)$_GET['id'];
$action = $_GET['action'];

mysqli_begin_transaction($conn);

try{

    // Get organizer + user
    $res = mysqli_query($conn,"SELECT user_id FROM organizers WHERE id=$org_id");
    $org = mysqli_fetch_assoc($res);

    if(!$org){
        throw new Exception("Organizer not found");
    }

    $user_id = (int)$org['user_id'];
    $admin_id = (int)$_SESSION['user_id'];

    /* ================= ACTION SWITCH ================= */

    if($action == "approve"){

        mysqli_query($conn,"UPDATE organizers SET status='approved' WHERE id=$org_id");
        mysqli_query($conn,"UPDATE users SET role='organizer' WHERE id=$user_id");

        $log = "Organizer approved (ID: $org_id)";

    }
    elseif($action == "reject"){

        mysqli_query($conn,"UPDATE organizers SET status='rejected' WHERE id=$org_id");
        mysqli_query($conn,"UPDATE users SET role='user' WHERE id=$user_id");

        $log = "Organizer rejected (ID: $org_id)";

    }
    elseif($action == "cancel_license"){

        mysqli_query($conn,"
            UPDATE organizers 
            SET status='rejected',
                license_status='cancelled'
            WHERE id=$org_id
        ");

        mysqli_query($conn,"UPDATE users SET role='user' WHERE id=$user_id");

        mysqli_query($conn,"
            UPDATE events 
            SET is_active=0 
            WHERE organizer_id=$org_id
        ");

        $log = "Organizer license cancelled (ID: $org_id)";

    }
    elseif($action == "delete"){

        mysqli_query($conn,"DELETE FROM events WHERE organizer_id=$org_id");
        mysqli_query($conn,"DELETE FROM organizers WHERE id=$org_id");
        mysqli_query($conn,"UPDATE users SET role='user' WHERE id=$user_id");

        $log = "Organizer deleted (ID: $org_id)";

    }
    else{
        throw new Exception("Invalid action");
    }

    // Insert Admin Log
    mysqli_query($conn,"
        INSERT INTO admin_logs(admin_id,action)
        VALUES($admin_id,'$log')
    ");

    mysqli_commit($conn);

}catch(Exception $e){
    mysqli_rollback($conn);
    die("Error: ".$e->getMessage());
}

header("Location: organizers_list.php");
exit;
?>
