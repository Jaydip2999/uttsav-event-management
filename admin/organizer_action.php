<?php
require_once("admin_check.php");
require_once("../includes/db.php");

if(!isset($_GET['id'], $_GET['action'])){
    header("Location: organizers_list.php");
    exit;
}

$org_id = (int)$_GET['id'];
$action = $_GET['action'];
$admin_id = (int)$_SESSION['user_id'];

mysqli_begin_transaction($conn);

try {

    /* ===== FETCH ORGANIZER ===== */
    $res = mysqli_query($conn,"SELECT id, user_id, status FROM organizers WHERE id=$org_id");
    $org = mysqli_fetch_assoc($res);

    if(!$org){
        throw new Exception("Organizer not found");
    }

    $user_id = (int)$org['user_id'];

    /* ===== ACTION SWITCH ===== */

    /* ---------- APPROVE / RE-APPROVE ---------- */
    if($action === "approve"){

        mysqli_query($conn,"
            UPDATE organizers 
            SET status='approved' 
            WHERE id=$org_id
        ");

        mysqli_query($conn,"
            UPDATE users 
            SET role='organizer', status='active' 
            WHERE id=$user_id
        ");

        // Notification
        mysqli_query($conn,"
            INSERT INTO notifications (user_id,title,message,type)
            VALUES (
                $user_id,
                'Organizer Approved',
                'Your organizer account has been approved by admin.',
                'organizer'
            )
        ");

        $log = "Organizer approved / re-approved (ID: $org_id)";
    }

    /* ---------- REJECT ---------- */
    elseif($action === "reject"){

        mysqli_query($conn,"
            UPDATE organizers 
            SET status='rejected' 
            WHERE id=$org_id
        ");

        mysqli_query($conn,"
            UPDATE users 
            SET role='user' 
            WHERE id=$user_id
        ");

        mysqli_query($conn,"
            INSERT INTO notifications (user_id,title,message,type)
            VALUES (
                $user_id,
                'Organizer Request Rejected',
                'Your organizer request has been rejected by admin.',
                'organizer'
            )
        ");

        $log = "Organizer rejected (ID: $org_id)";
    }

    /* ---------- CANCEL LICENSE ---------- */
    elseif($action === "cancel_license"){

        mysqli_query($conn,"
            UPDATE organizers 
            SET status='license_cancelled' 
            WHERE id=$org_id
        ");

        mysqli_query($conn,"
            UPDATE users 
            SET role='user', status='active' 
            WHERE id=$user_id
        ");

        mysqli_query($conn,"
            UPDATE events 
            SET status='closed' 
            WHERE organizer_id=$org_id
        ");

        mysqli_query($conn,"
            INSERT INTO notifications (user_id,title,message,type)
            VALUES (
                $user_id,
                'License Cancelled',
                'Your organizer license has been cancelled by admin.',
                'organizer'
            )
        ");

        $log = "Organizer license cancelled (ID: $org_id)";
    }

    /* ---------- REACTIVATE LICENSE ---------- */
    elseif($action === "reactivate"){

        mysqli_query($conn,"
            UPDATE organizers 
            SET status='approved' 
            WHERE id=$org_id
        ");

        mysqli_query($conn,"
            UPDATE users 
            SET role='organizer', status='active' 
            WHERE id=$user_id
        ");

        mysqli_query($conn,"
            INSERT INTO notifications (user_id,title,message,type)
            VALUES (
                $user_id,
                'License Reactivated',
                'Your organizer license has been reactivated.',
                'organizer'
            )
        ");

        $log = "Organizer license reactivated (ID: $org_id)";
    }

    /* ---------- DELETE ---------- */
    elseif($action === "delete"){

        mysqli_query($conn,"DELETE FROM events WHERE organizer_id=$org_id");
        mysqli_query($conn,"DELETE FROM organizers WHERE id=$org_id");
        mysqli_query($conn,"UPDATE users SET role='user' WHERE id=$user_id");

        $log = "Organizer deleted (ID: $org_id)";
    }

    else{
        throw new Exception("Invalid action");
    }

    /* ===== ADMIN LOG ===== */
    mysqli_query($conn,"
        INSERT INTO admin_logs (admin_id, action)
        VALUES ($admin_id, '$log')
    ");

    mysqli_commit($conn);

}catch(Exception $e){
    mysqli_rollback($conn);
    die("Error: ".$e->getMessage());
}

header("Location: organizers_list.php");
exit;
