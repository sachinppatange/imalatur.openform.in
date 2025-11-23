<?php
class Students
{
    private $studentTable = "student"; 
    public $studid;
    public $pamount;
    public $pstatus;
    public $pdate;
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }
    function update()
    {

        $stmt = $this->conn->prepare("
        UPDATE " . $this->studentTable . " 
        SET pamount = ?, pstatus = ?, pdate = ?
        WHERE stud_id = ?"
    );
    
    // Sanitize the input
    $this->studid = htmlspecialchars(strip_tags($this->studid));
    $this->pamount = htmlspecialchars(strip_tags($this->pamount));
    $this->pstatus = htmlspecialchars(strip_tags($this->pstatus));
    $this->pdate = htmlspecialchars(strip_tags($this->pdate));
    
    // Bind parameters
    $stmt->bind_param('issi', $this->pamount, $this->pstatus, $this->pdate, $this->studid);



        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

}

?>