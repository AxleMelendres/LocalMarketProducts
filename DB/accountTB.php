    <?php 

    class account {

        private $conn;
        private $tbl_name = "account"; 

        public $id;
        public $name;
        public $uname;
        public $email;
        public $contact;
        public $password;
        public $purpose;
        public $district;

        public function __construct($db) {
            $this->conn = $db;
        }

        public function register(){
            // Check if terms are accepted
            if (!isset($_POST['terms']) || $_POST['terms'] !== 'accepted') {
                die("You must agree that the information you provided are valid and correct.");
            }

            // Get the POST data
            $this->name = $_POST['name'];
            $this->uname = $_POST['username'];
            $this->email = $_POST['email'];
            $this->contact = $_POST['contact'] ?? '';  // Default empty if not provided
            $this->password = $_POST['password'];
            $this->purpose = $_POST['purpose'];
            $this->district = $_POST['district'];

            // Hash the password
            $hashedPassword = password_hash($this->password, PASSWORD_DEFAULT);

            // Prepare the query with named placeholders
            $query = "INSERT INTO " . $this->tbl_name . " 
                    (`Full Name`, Username, Email, `Contact Number`, Password, Purpose, District) 
                    VALUES (:name, :username, :email, :contact, :password, :purpose, :district)";
            
            // Prepare the statement
            $stmt = $this->conn->prepare($query);

            // Bind the parameters to the placeholders
            $stmt->bindParam(':name', $this->name);
            $stmt->bindParam(':username', $this->uname);
            $stmt->bindParam(':email', $this->email);
            $stmt->bindParam(':contact', $this->contact);
            $stmt->bindParam(':password', $hashedPassword);
            $stmt->bindParam(':purpose', $this->purpose);
            $stmt->bindParam(':district', $this->district);

            // Execute the query and check if it was successful
            if ($stmt->execute()) {
                return true;  // Registration successful
            } else {
                echo "Error: " . $stmt->errorInfo()[2];
                return false;  // Registration failed
            }
        }


                    } else {
                        echo "Invalid password. Please try again.";
                    }
                } else {
                    echo "No account found with that username.";
                }
            } else {
                echo "Error: " . $stmt->errorInfo()[2];
            }

        }

    }

    ?>
