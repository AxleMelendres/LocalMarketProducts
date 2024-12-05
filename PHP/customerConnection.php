<?php
class Customer {
    private $conn;
    private $tbl_name = "buyer";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getCustomerDetails($username) {
        $query = "SELECT 
                    a.full_name AS full_name, 
                    a.Username, 
                    a.Email, 
                    a.`Contact Number`, 
                    a.Purpose, 
                    a.District, 
                    b.buyer_image 
                  FROM account a 
                  LEFT JOIN buyer b ON a.account_id = b.account_id 
                  WHERE a.Username = :username";
    
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();
    
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
        // Return false if no data is found
        return $result ?: false;
    }

    public function updateCustomerDetails($username, $full_name, $email, $contact_number, $district, $profile_picture = null) {
        try {
            // Update account details
            $update_account_query = "UPDATE account SET 
                full_name = :full_name, 
                Email = :email, 
                `Contact Number` = :contact_number, 
                District = :district 
                WHERE Username = :username";
            
            $stmt = $this->conn->prepare($update_account_query);
            $stmt->bindParam(':full_name', $full_name);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':contact_number', $contact_number);
            $stmt->bindParam(':district', $district);
            $stmt->bindParam(':username', $username);
            $stmt->execute();

            // Handle profile picture upload
            if ($profile_picture && $profile_picture['error'] == 0) {
                $file_tmp = $profile_picture['tmp_name'];
                $file_name = $profile_picture['name'];
                $file_ext = pathinfo($file_name, PATHINFO_EXTENSION);
                $allowed_exts = ['jpg', 'jpeg', 'png', 'gif'];

                if (in_array(strtolower($file_ext), $allowed_exts)) {
                    $upload_dir = '../uploads/';
                    $new_file_name = uniqid() . '.' . $file_ext;
                    $file_path = $upload_dir . $new_file_name;

                    if (move_uploaded_file($file_tmp, $file_path)) {
                        // Update the profile picture in the database
                        $update_image_query = "UPDATE buyer SET buyer_image = :buyer_image WHERE account_id = (SELECT account_id FROM account WHERE Username = :username)";
                        $stmt = $this->conn->prepare($update_image_query);
                        $stmt->bindParam(':buyer_image', $file_path);
                        $stmt->bindParam(':username', $username);
                        $stmt->execute();
                    } else {
                        throw new Exception("Error uploading the profile picture.");
                    }
                } else {
                    throw new Exception("Invalid file type. Only JPG, JPEG, PNG, and GIF files are allowed.");
                }
            }

            return "Account details updated successfully!";
        } catch (Exception $e) {
            return "Error updating account details: " . $e->getMessage();
        }
    }
}
?>
