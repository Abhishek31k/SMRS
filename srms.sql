-- --------------------------------------------------------
-- Database: `srms`
-- --------------------------------------------------------
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";
DROP TABLE IF EXISTS tblattendance;
DROP TABLE IF EXISTS tblfees;
DROP TABLE IF EXISTS tblresult;
DROP TABLE IF EXISTS tblstaffsubjects;
DROP TABLE IF EXISTS tblstudents;
DROP TABLE IF EXISTS tblsubjects;
DROP TABLE IF EXISTS tblclasses;
DROP TABLE IF EXISTS tblnotice;
DROP TABLE IF EXISTS tblstaff;
DROP TABLE IF EXISTS admin;

-- --------------------------------------------------------
-- Table structure for table `admin`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `admin`;
CREATE TABLE `admin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `UserName` varchar(100) DEFAULT NULL,
  `Password` varchar(100) DEFAULT NULL,
  `updationDate` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `admin` (`UserName`, `Password`, `updationDate`) VALUES
('admin', MD5('admin123'), '2024-03-10 10:30:57');

-- --------------------------------------------------------
-- Table structure for table `tblclasses`
-- --------------------------------------------------------
DROP TABLE IF EXISTS tblclasses;

CREATE TABLE tblclasses (
  id INT(11) NOT NULL AUTO_INCREMENT,
  Branch VARCHAR(100) NOT NULL,         -- e.g., CSE, ECE, MECH
  Semester VARCHAR(10) NOT NULL,        -- e.g., 1-1, 1-2, 2-1, 2-2, 3-1, 3-2, 4-1, 4-2
  Section VARCHAR(10) NOT NULL,         -- e.g., A, B, C
  Regulation VARCHAR(20) NOT NULL,      -- e.g., R20, R23
  BatchStart YEAR NOT NULL,             -- e.g., 2023
  BatchEnd YEAR NOT NULL,               -- e.g., 2027

  -- Derived IDs for easier operations
  BranchId VARCHAR(100) GENERATED ALWAYS AS (
      CONCAT(Branch, '-', BatchStart, '-', BatchEnd)
  ) STORED,
  ClassId VARCHAR(120) GENERATED ALWAYS AS (
      CONCAT(Branch, '-', BatchStart, '-', BatchEnd, '-', Section)
  ) STORED,

  CreationDate TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Batch 2023–2027, Semester 1-1
INSERT INTO tblclasses (Branch, Semester, Section, Regulation, BatchStart, BatchEnd)
VALUES 
('CSE',   '1-1', 'A', 'R20', 2023, 2027),
('CSE',   '1-1', 'B', 'R20', 2023, 2027),
('ECE',   '1-1', 'A', 'R20', 2023, 2027),
('ECE',   '1-1', 'B', 'R20', 2023, 2027),
('MECH',  '1-1', 'A', 'R20', 2023, 2027),
('CIVIL', '1-1', 'A', 'R20', 2023, 2027);

-- Batch 2022–2026, Semester 2-1
INSERT INTO tblclasses (Branch, Semester, Section, Regulation, BatchStart, BatchEnd)
VALUES 
('CSE',   '2-1', 'A', 'R20', 2022, 2026),
('CSE',   '2-1', 'B', 'R20', 2022, 2026),
('ECE',   '2-1', 'A', 'R20', 2022, 2026),
('MECH',  '2-1', 'A', 'R20', 2022, 2026),
('CIVIL', '2-1', 'A', 'R20', 2022, 2026);

-- Batch 2021–2025, Semester 3-1
INSERT INTO tblclasses (Branch, Semester, Section, Regulation, BatchStart, BatchEnd)
VALUES 
('CSE',   '3-1', 'A', 'R19', 2021, 2025),
('ECE',   '3-1', 'A', 'R19', 2021, 2025),
('ECE',   '3-1', 'B', 'R19', 2021, 2025),
('MECH',  '3-1', 'A', 'R19', 2021, 2025),
('CIVIL', '3-1', 'A', 'R19', 2021, 2025);

-- Batch 2020–2024, Semester 4-1
INSERT INTO tblclasses (Branch, Semester, Section, Regulation, BatchStart, BatchEnd)
VALUES 
('CSE',   '4-1', 'A', 'R18', 2020, 2024),
('ECE',   '4-1', 'A', 'R18', 2020, 2024),
('MECH',  '4-1', 'A', 'R18', 2020, 2024),
('MECH',  '4-1', 'B', 'R18', 2020, 2024),
('CIVIL', '4-1', 'A', 'R18', 2020, 2024);



-- --------------------------------------------------------
-- Table structure for table `tblnotice`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `tblnotice`;
CREATE TABLE `tblnotice` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `noticeTitle` VARCHAR(255) NOT NULL,
  `noticeDetails` MEDIUMTEXT NOT NULL,
  `postedBy` VARCHAR(100) NOT NULL,               -- Admin username / Staff name
  `role` ENUM('Admin','Staff') NOT NULL,          -- Who posted the notice
  `postingDate` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Sample Data
-- --------------------------------------------------------
INSERT INTO `tblnotice` (`noticeTitle`, `noticeDetails`, `postedBy`, `role`, `postingDate`) VALUES
('Notice regarding Result Declaration', 'Results for semester exams will be declared on 10th May.', 'Principal', 'Admin', '2024-05-01 14:34:58'),
('Test Notice', 'This is for testing purposes only.', 'System Admin', 'Admin', '2024-05-02 14:48:32'),
('Lab Cancelled', 'Physics lab is cancelled today due to maintenance.', 'Prof. John Doe', 'Staff', NOW());

-- --------------------------------------------------------
-- Table structure for table `tblsubjects`
-- --------------------------------------------------------
-- Drop existing table
DROP TABLE IF EXISTS `tblsubjects`;

-- Recreate table
CREATE TABLE `tblsubjects` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `SubjectName` varchar(100) NOT NULL,
  `SubjectCode` varchar(100) DEFAULT NULL,
  `ClassId` int(11) NOT NULL,
  `Creationdate` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `UpdationDate` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  CONSTRAINT fk_subjects_class FOREIGN KEY (ClassId) REFERENCES tblclasses(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;




-- Subjects for Batch 2023–2027 (Semester 1-1)
INSERT INTO tblsubjects (SubjectName, SubjectCode, ClassId) VALUES
('Programming in C', 'CSE101', 1),
('Mathematics-I', 'MATH101', 1),
('English Communication', 'ENG101', 1),

('Programming in C', 'CSE102', 2),
('Mathematics-I', 'MATH102', 2),
('Physics', 'PHY101', 2),

('Basic Electronics', 'ECE101', 3),
('Mathematics-I', 'MATH103', 3),
('Engineering Physics', 'PHY102', 3),

('Basic Electronics', 'ECE102', 4),
('Mathematics-I', 'MATH104', 4),
('Engineering Chemistry', 'CHEM101', 4),

('Engineering Drawing', 'MECH101', 5),
('Mathematics-I', 'MATH105', 5),
('Applied Mechanics', 'MECH102', 5),

('Surveying', 'CIV101', 6),
('Mathematics-I', 'MATH106', 6),
('Engineering Mechanics', 'CIV102', 6);


-- Subjects for Batch 2022–2026 (Semester 2-1)
INSERT INTO tblsubjects (SubjectName, SubjectCode, ClassId) VALUES
('Data Structures', 'CSE201', 7),
('Discrete Mathematics', 'MATH201', 7),
('Digital Logic', 'CSE202', 7),

('Data Structures', 'CSE203', 8),
('Discrete Mathematics', 'MATH202', 8),
('Environmental Studies', 'EVS201', 8),

('Signals & Systems', 'ECE201', 9),
('Electronic Devices', 'ECE202', 9),
('Network Theory', 'ECE203', 9),

('Thermodynamics', 'MECH201', 10),
('Mechanics of Materials', 'MECH202', 10),
('Fluid Mechanics', 'MECH203', 10),

('Surveying-II', 'CIV201', 11),
('Strength of Materials', 'CIV202', 11),
('Structural Analysis-I', 'CIV203', 11);


-- Subjects for Batch 2021–2025 (Semester 3-1)
INSERT INTO tblsubjects (SubjectName, SubjectCode, ClassId) VALUES
('Database Management Systems', 'CSE301', 12),
('Operating Systems', 'CSE302', 12),
('Computer Networks', 'CSE303', 12),

('Analog Communication', 'ECE301', 13),
('Control Systems', 'ECE302', 13),
('Electromagnetic Theory', 'ECE303', 13),

('Digital Communication', 'ECE304', 14),
('Microprocessors', 'ECE305', 14),
('VLSI Design', 'ECE306', 14),

('Machine Design', 'MECH301', 15),
('Heat Transfer', 'MECH302', 15),
('Manufacturing Technology-II', 'MECH303', 15),

('Concrete Technology', 'CIV301', 16),
('Hydrology', 'CIV302', 16),
('Structural Analysis-II', 'CIV303', 16);


-- Subjects for Batch 2020–2024 (Semester 4-1)
INSERT INTO tblsubjects (SubjectName, SubjectCode, ClassId) VALUES
('Compiler Design', 'CSE401', 17),
('Artificial Intelligence', 'CSE402', 17),
('Software Engineering', 'CSE403', 17),

('Microwave Engineering', 'ECE401', 18),
('Embedded Systems', 'ECE402', 18),
('Wireless Communication', 'ECE403', 18),

('Automobile Engineering', 'MECH401', 19),
('Industrial Engineering', 'MECH402', 19),
('Finite Element Methods', 'MECH403', 19),

('Robotics', 'MECH404', 20),
('CAD/CAM', 'MECH405', 20),
('Refrigeration & Air Conditioning', 'MECH406', 20),

('Advanced Structural Design', 'CIV401', 21),
('Transportation Engineering', 'CIV402', 21),
('Geotechnical Engineering-II', 'CIV403', 21);

-- --------------------------------------------------------
-- Table structure for `tblstaff`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `tblstaff`;
CREATE TABLE `tblstaff` (
    `StaffId` INT AUTO_INCREMENT PRIMARY KEY,
    `StaffCode` VARCHAR(50) UNIQUE NOT NULL,
    `Gender` ENUM('Male','Female') NOT NULL,
    `StaffName` VARCHAR(100) NOT NULL,
    `Email` VARCHAR(100) UNIQUE NOT NULL,
    `Mobile` VARCHAR(15) NOT NULL,
    `Password` VARCHAR(255) NOT NULL,
    `Role` VARCHAR(50) DEFAULT 'Staff',
    `Department` VARCHAR(100) DEFAULT NULL,
    `Designation` VARCHAR(100) DEFAULT NULL,
    `Status` ENUM('Active','Inactive') DEFAULT 'Active',
    `Photo` VARCHAR(255) DEFAULT NULL,
    `CreatedAt` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Sample Data for `tblstaff`
-- Passwords are MD5 of 'password123'
-- --------------------------------------------------------
-- HODs
INSERT INTO tblstaff (StaffCode, StaffName, Gender, Email, Mobile, Password, Role, Department, Designation, Status)
VALUES
('HODCSE',   'Dr. Ramesh Kumar', 'Male', 'hod.cse@example.com', '9000000001', MD5('password123'), 'HOD', 'CSE', 'Head of Department', 'Active'),
('HODIT',    'Dr. Anita Devi', 'Female', 'hod.it@example.com', '9000000002', MD5('password123'), 'HOD', 'IT', 'Head of Department', 'Active'),
('HODECE',   'Dr. Suresh Rao', 'Male', 'hod.ece@example.com', '9000000003', MD5('password123'), 'HOD', 'ECE', 'Head of Department', 'Active'),
('HODEEE',   'Dr. Kavitha Sharma', 'Female', 'hod.eee@example.com', '9000000004', MD5('password123'), 'HOD', 'EEE', 'Head of Department', 'Active'),
('HODMECH',  'Dr. Prakash Reddy', 'Male', 'hod.mech@example.com', '9000000005', MD5('password123'), 'HOD', 'MECH', 'Head of Department', 'Active'),
('HODCIVIL', 'Dr. Lakshmi Narayana', 'Male', 'hod.civil@example.com', '9000000006', MD5('password123'), 'HOD', 'CIVIL', 'Head of Department', 'Active');

-- Staff for CSE
INSERT INTO tblstaff (StaffCode, StaffName, Gender, Email, Mobile, Password, Role, Department, Designation, Status) VALUES
('STFCSE1', 'Arun Kumar', 'Male', 'arun.cse@example.com', '9111111111', MD5('password123'), 'Staff', 'CSE', 'Assistant Professor', 'Active'),
('STFCSE2', 'Priya Sharma', 'Female', 'priya.cse@example.com', '9111111112', MD5('password123'), 'Staff', 'CSE', 'Lecturer', 'Active'),
('STFCSE3', 'Vivek Singh', 'Male', 'vivek.cse@example.com', '9111111113', MD5('password123'), 'Staff', 'CSE', 'Professor', 'Active');

-- Staff for IT
INSERT INTO tblstaff (StaffCode, StaffName, Gender, Email, Mobile, Password, Role, Department, Designation, Status) VALUES
('STFIT1', 'Neha Verma', 'Female', 'neha.it@example.com', '9222222221', MD5('password123'), 'Staff', 'IT', 'Assistant Professor', 'Active'),
('STFIT2', 'Rajesh Gupta', 'Male', 'rajesh.it@example.com', '9222222222', MD5('password123'), 'Staff', 'IT', 'Lecturer', 'Active'),
('STFIT3', 'Anjali Nair', 'Female', 'anjali.it@example.com', '9222222223', MD5('password123'), 'Staff', 'IT', 'Professor', 'Active');

-- Staff for ECE
INSERT INTO tblstaff (StaffCode, StaffName, Gender, Email, Mobile, Password, Role, Department, Designation, Status) VALUES
('STFECE1', 'Sandeep Kumar', 'Male', 'sandeep.ece@example.com', '9333333331', MD5('password123'), 'Staff', 'ECE', 'Assistant Professor', 'Active'),
('STFECE2', 'Meena Iyer', 'Female', 'meena.ece@example.com', '9333333332', MD5('password123'), 'Staff', 'ECE', 'Lecturer', 'Active'),
('STFECE3', 'Kiran Reddy', 'Male', 'kiran.ece@example.com', '9333333333', MD5('password123'), 'Staff', 'ECE', 'Professor', 'Active');

-- Staff for EEE
INSERT INTO tblstaff (StaffCode, StaffName, Gender, Email, Mobile, Password, Role, Department, Designation, Status) VALUES
('STFEEE1', 'Alok Das', 'Male', 'alok.eee@example.com', '9444444441', MD5('password123'), 'Staff', 'EEE', 'Assistant Professor', 'Active'),
('STFEEE2', 'Pooja Mishra', 'Female', 'pooja.eee@example.com', '9444444442', MD5('password123'), 'Staff', 'EEE', 'Lecturer', 'Active'),
('STFEEE3', 'Deepak Yadav', 'Male', 'deepak.eee@example.com', '9444444443', MD5('password123'), 'Staff', 'EEE', 'Professor', 'Active');

-- Staff for MECH
INSERT INTO tblstaff (StaffCode, StaffName, Gender, Email, Mobile, Password, Role, Department, Designation, Status) VALUES
('STFMECH1', 'Rohit Kumar', 'Male', 'rohit.mech@example.com', '9555555551', MD5('password123'), 'Staff', 'MECH', 'Assistant Professor', 'Active'),
('STFMECH2', 'Sneha Rani', 'Female', 'sneha.mech@example.com', '9555555552', MD5('password123'), 'Staff', 'MECH', 'Lecturer', 'Active'),
('STFMECH3', 'Amit Sharma', 'Male', 'amit.mech@example.com', '9555555553', MD5('password123'), 'Staff', 'MECH', 'Professor', 'Active');

-- Staff for CIVIL
INSERT INTO tblstaff (StaffCode, StaffName, Gender, Email, Mobile, Password, Role, Department, Designation, Status) VALUES
('STFCIVIL1', 'Sunil Kumar', 'Male', 'sunil.civil@example.com', '9666666661', MD5('password123'), 'Staff', 'CIVIL', 'Assistant Professor', 'Active'),
('STFCIVIL2', 'Ritika Sharma', 'Female', 'ritika.civil@example.com', '9666666662', MD5('password123'), 'Staff', 'CIVIL', 'Lecturer', 'Active'),
('STFCIVIL3', 'Ajay Singh', 'Male', 'ajay.civil@example.com', '9666666663', MD5('password123'), 'Staff', 'CIVIL', 'Professor', 'Active');

-- --------------------------------------------------------
-- Table structure for table `tblstudents`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `tblstudents`;
CREATE TABLE `tblstudents` (
  `RegNo` VARCHAR(50) PRIMARY KEY,     
  `StudentName` VARCHAR(100) NOT NULL,
  `DOB` DATE,
  `Gender` VARCHAR(10),
  `StudentEmail` VARCHAR(100),
  `Password` VARCHAR(255),             -- NEW Password column
  `StudentMobile` VARCHAR(15),
  `FatherName` VARCHAR(100),
  `FatherMobile` VARCHAR(15),
  `AdmissionType` VARCHAR(50),
  `CounsellorName` VARCHAR(100),
  `CounsellorMobile` VARCHAR(15),
  `CounsellorId` INT NULL,  
  `ClassId` INT NOT NULL,
  `RegDate` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `Photo` VARCHAR(255) DEFAULT 'default.png',
  FOREIGN KEY (`ClassId`) REFERENCES `tblclasses`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`CounsellorId`) REFERENCES `tblstaff`(`StaffId`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Insert students (with Password = MD5(RegNo))
INSERT INTO tblstudents
(RegNo, StudentName, DOB, Gender, StudentEmail, Password, StudentMobile, FatherName, FatherMobile, AdmissionType, CounsellorName, CounsellorMobile, ClassId)
VALUES
('22B81A0501', 'ABHISHEK KUMAR YADAV', '1999-01-01', 'Male', 'abc@gmail.com', MD5('22B81A0501'), '9999999990', 'Mr. Yadav', '8888888880', 'Regular', 'Counsellor A', '7777777770', 1),
('22B81A0502', 'ACHANTA SOWMYA SRI', '1999-01-01', 'Female', 'abc@gmail.com', MD5('22B81A0502'), '9999999991', 'Mr. Sowmya', '8888888881', 'Regular', 'Counsellor A', '7777777771', 1),
('22B81A0503', 'ADIGARLA SAI DEVI SRI PRASAD', '1999-01-01', 'Male', 'abc@gmail.com', MD5('22B81A0503'), '9999999992', 'Mr. Prasad', '8888888882', 'Regular', 'Counsellor A', '7777777772', 1),
('22B81A0504', 'AKULA KANTHI SRI', '1999-01-01', 'Female', 'abc@gmail.com', MD5('22B81A0504'), '9999999993', 'Mr. Akula', '8888888883', 'Regular', 'Counsellor A', '7777777773', 1),
('22B81A0505', 'AKULA SATYANARAYANA', '1999-01-01', 'Male', 'abc@gmail.com', MD5('22B81A0505'), '9999999994', 'Mr. Satya', '8888888884', 'Regular', 'Counsellor A', '7777777774', 1),
('23B85A0501', 'BYRIPINDI MURALI KRISHNA', '1999-01-01', 'Male', 'abc@gmail.com', MD5('23B85A0501'), '9999999995', 'Mr. Murali', '8888888885', 'Regular', 'Counsellor B', '7777777775', 2),
('23B85A0502', 'CHEDE YASWANTH', '1999-01-01', 'Male', 'abc@gmail.com', MD5('23B85A0502'), '9999999996', 'Mr. Chede', '8888888886', 'Regular', 'Counsellor B', '7777777776', 2),
('23B85A0503', 'DESIREDDY GOPAL REDDY', '1999-01-01', 'Male', 'abc@gmail.com', MD5('23B85A0503'), '9999999997', 'Mr. Reddy', '8888888887', 'Regular', 'Counsellor B', '7777777777', 2),
('23B85A0504', 'JAVVAJI NAVYA', '1999-01-01', 'Female', 'abc@gmail.com', MD5('23B85A0504'), '9999999998', 'Mr. Navya', '8888888888', 'Regular', 'Counsellor B', '7777777778', 2),
('23B85A0505', 'KASTURI GOWTHAM', '1999-01-01', 'Male', 'abc@gmail.com', MD5('23B85A0505'), '9999999999', 'Mr. Gowtham', '8888888889', 'Regular', 'Counsellor B', '7777777779', 2);

-- Students for Batch 2023–2027 (Semester 1-1)
INSERT INTO tblstudents 
(RegNo, StudentName, DOB, Gender, StudentEmail, Password, StudentMobile, FatherName, FatherMobile, AdmissionType, CounsellorName, CounsellorMobile, ClassId)
VALUES
('23CSE001', 'Amit Kumar', '2005-01-15', 'Male', 'amit.cseA@example.com', MD5('23CSE001'), '9991110001', 'Raj Kumar', '8881110001', 'Regular', 'Counsellor CSE-A', '7771110001', 1),
('23CSE002', 'Sneha Reddy', '2005-03-20', 'Female', 'sneha.cseA@example.com', MD5('23CSE002'), '9991110002', 'Mohan Reddy', '8881110002', 'Regular', 'Counsellor CSE-A', '7771110002', 1),
('23CSE101', 'Arjun Singh', '2005-02-10', 'Male', 'arjun.cseB@example.com', MD5('23CSE101'), '9991120001', 'Vikram Singh', '8881120001', 'Regular', 'Counsellor CSE-B', '7771120001', 2),

('23ECE001', 'Kiran Kumar', '2005-04-18', 'Male', 'kiran.eceA@example.com', MD5('23ECE001'), '9991130001', 'Suresh Kumar', '8881130001', 'Regular', 'Counsellor ECE-A', '7771130001', 3),
('23ECE002', 'Priya Sharma', '2005-05-22', 'Female', 'priya.eceA@example.com', MD5('23ECE002'), '9991130002', 'Arun Sharma', '8881130002', 'Regular', 'Counsellor ECE-A', '7771130002', 3),
('23ECE101', 'Rahul Das', '2005-07-10', 'Male', 'rahul.eceB@example.com', MD5('23ECE101'), '9991140001', 'Deepak Das', '8881140001', 'Regular', 'Counsellor ECE-B', '7771140001', 4),

('23MECH001', 'Sanjay Patel', '2005-08-05', 'Male', 'sanjay.mech@example.com', MD5('23MECH001'), '9991150001', 'Ramesh Patel', '8881150001', 'Regular', 'Counsellor MECH-A', '7771150001', 5),
('23CIV001', 'Anjali Verma', '2005-09-12', 'Female', 'anjali.civil@example.com', MD5('23CIV001'), '9991160001', 'Mahesh Verma', '8881160001', 'Regular', 'Counsellor CIVIL-A', '7771160001', 6);

-- Students for Batch 2022–2026 (Semester 2-1)
INSERT INTO tblstudents (RegNo, StudentName, DOB, Gender, StudentEmail, Password, StudentMobile,
 FatherName, FatherMobile, AdmissionType, CounsellorName, CounsellorMobile, ClassId) VALUES
('22CSE001', 'Rohit Yadav', '2004-01-10', 'Male', 'rohit.cseA@example.com', MD5('22CSE001'), '9991210001', 'Sanjay Yadav', '8881210001', 'Regular', 'Counsellor CSE-A', '7771210001', 7),
('22CSE002', 'Neha Gupta', '2004-02-22', 'Female', 'neha.cseA@example.com', MD5('22CSE002'), '9991210002', 'Akhil Gupta', '8881210002', 'Regular', 'Counsellor CSE-A', '7771210002', 7),
('22CSE101', 'Vikas Rao', '2004-03-18', 'Male', 'vikas.cseB@example.com', MD5('22CSE101'), '9991220001', 'Krishna Rao', '8881220001', 'Regular', 'Counsellor CSE-B', '7771220001', 8),

('22ECE001', 'Meena Iyer', '2004-04-12', 'Female', 'meena.ece@example.com', MD5('22ECE001'), '9991230001', 'Anil Iyer', '8881230001', 'Regular', 'Counsellor ECE-A', '7771230001', 9),
('22MECH001', 'Harish Nair', '2004-05-09', 'Male', 'harish.mech@example.com', MD5('22MECH001'), '9991240001', 'Ravi Nair', '8881240001', 'Regular', 'Counsellor MECH-A', '7771240001', 10),
('22CIV001', 'Pooja Das', '2004-06-17', 'Female', 'pooja.civil@example.com', MD5('22CIV001'), '9991250001', 'Arvind Das', '8881250001', 'Regular', 'Counsellor CIVIL-A', '7771250001', 11);

-- Students for Batch 2021–2025 (Semester 3-1)
INSERT INTO tblstudents (RegNo, StudentName, DOB, Gender, StudentEmail, Password, StudentMobile,
 FatherName, FatherMobile, AdmissionType, CounsellorName, CounsellorMobile, ClassId) VALUES
('21CSE001', 'Ankit Sharma', '2003-02-25', 'Male', 'ankit.cse@example.com', MD5('21CSE001'), '9991310001', 'Prakash Sharma', '8881310001', 'Regular', 'Counsellor CSE-A', '7771310001', 12),
('21ECE001', 'Divya Rani', '2003-03-14', 'Female', 'divya.ece@example.com', MD5('21ECE001'), '9991320001', 'Raghu Rani', '8881320001', 'Regular', 'Counsellor ECE-A', '7771320001', 13),
('21ECE101', 'Suraj Kumar', '2003-04-28', 'Male', 'suraj.ece@example.com', MD5('21ECE101'), '9991330001', 'Naresh Kumar', '8881330001', 'Regular', 'Counsellor ECE-B', '7771330001', 14),
('21MECH001', 'Preeti Sinha', '2003-05-11', 'Female', 'preeti.mech@example.com', MD5('21MECH001'), '9991340001', 'Dinesh Sinha', '8881340001', 'Regular', 'Counsellor MECH-A', '7771340001', 15),
('21CIV001', 'Alok Jain', '2003-06-02', 'Male', 'alok.civil@example.com', MD5('21CIV001'), '9991350001', 'Sanjay Jain', '8881350001', 'Regular', 'Counsellor CIVIL-A', '7771350001', 16);

-- Students for Batch 2020–2024 (Semester 4-1)
INSERT INTO tblstudents (RegNo, StudentName, DOB, Gender, StudentEmail, Password, StudentMobile,
 FatherName, FatherMobile, AdmissionType, CounsellorName, CounsellorMobile, ClassId) VALUES
('20CSE001', 'Ramesh Choudhary', '2002-01-05', 'Male', 'ramesh.cse@example.com', MD5('20CSE001'), '9991410001', 'Kailash Choudhary', '8881410001', 'Regular', 'Counsellor CSE-A', '7771410001', 17),
('20ECE001', 'Sunita Menon', '2002-02-11', 'Female', 'sunita.ece@example.com', MD5('20ECE001'), '9991420001', 'Ajay Menon', '8881420001', 'Regular', 'Counsellor ECE-A', '7771420001', 18),
('20MECH001', 'Deepak Yadav', '2002-03-07', 'Male', 'deepak.mech@example.com', MD5('20MECH001'), '9991430001', 'Vinod Yadav', '8881430001', 'Regular', 'Counsellor MECH-A', '7771430001', 19),
('20MECH101', 'Kavita Joshi', '2002-04-19', 'Female', 'kavita.mech@example.com', MD5('20MECH101'), '9991440001', 'Rajesh Joshi', '8881440001', 'Regular', 'Counsellor MECH-B', '7771440001', 20),
('20CIV001', 'Manish Agarwal', '2002-05-21', 'Male', 'manish.civil@example.com', MD5('20CIV001'), '9991450001', 'Sanjay Agarwal', '8881450001', 'Regular', 'Counsellor CIVIL-A', '7771450001', 21);

-- --------------------------------------------------------
-- Table structure for table `tblresult`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `tblresult`;

CREATE TABLE `tblresult` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `RegNo` VARCHAR(50) NOT NULL,             -- Student Register Number
  `Semester` VARCHAR(10) NOT NULL,          -- Semester like 1-1, 1-2, etc.
  `Subject` VARCHAR(100) NOT NULL,          -- Subject name
  `SubjectCode` VARCHAR(50) NOT NULL,       -- Subject code
  `Internals` INT(11) DEFAULT NULL,         -- Internal marks
  `Grade` VARCHAR(5) DEFAULT NULL,          -- Grade (A, B, etc.)
  `Credits` VARCHAR(5) DEFAULT NULL,        -- Credits
  `PostingDate` TIMESTAMP NULL DEFAULT current_timestamp(),
  `UpdationDate` TIMESTAMP NULL DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  CONSTRAINT fk_result_student FOREIGN KEY (`RegNo`) 
      REFERENCES `tblstudents`(`RegNo`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Sample Results for Semester 1-1 (CSE, ECE, MECH, CIVIL)
INSERT INTO tblresult (RegNo, Semester, Subject, SubjectCode, Internals, Grade, Credits)
VALUES
('23CSE001', '1-1', 'Programming in C', 'CSE101', 25, 'A', 4),
('23CSE001', '1-1', 'Mathematics-I', 'MATH101', 23, 'B+', 4),
('23CSE001', '1-1', 'English Communication', 'ENG101', 22, 'A', 3),

('23ECE001', '1-1', 'Basic Electronics', 'ECE101', 24, 'A', 4),
('23ECE001', '1-1', 'Mathematics-I', 'MATH103', 20, 'B', 4),
('23ECE001', '1-1', 'Engineering Physics', 'PHY102', 21, 'B+', 3),

('23MECH001', '1-1', 'Engineering Drawing', 'MECH101', 26, 'A+', 3),
('23MECH001', '1-1', 'Mathematics-I', 'MATH105', 24, 'A', 4),
('23MECH001', '1-1', 'Applied Mechanics', 'MECH102', 22, 'B+', 4),

('23CIV001', '1-1', 'Surveying', 'CIV101', 25, 'A', 3),
('23CIV001', '1-1', 'Mathematics-I', 'MATH106', 21, 'B+', 4),
('23CIV001', '1-1', 'Engineering Mechanics', 'CIV102', 23, 'A', 4);

-- Semester 1-2 Results for same students
INSERT INTO tblresult (RegNo, Semester, Subject, SubjectCode, Internals, Grade, Credits)
VALUES
('23CSE001', '1-2', 'Physics', 'PHY101', 24, 'A', 3),
('23CSE001', '1-2', 'Mathematics-II', 'MATH107', 22, 'B+', 4),
('23CSE001', '1-2', 'Data Structures (Intro)', 'CSE110', 20, 'B', 3),

('23ECE001', '1-2', 'Engineering Chemistry', 'CHEM101', 23, 'A', 3),
('23ECE001', '1-2', 'Mathematics-II', 'MATH108', 22, 'B+', 4),
('23ECE001', '1-2', 'Programming in C', 'CSE103', 24, 'A', 4);

-- Semester 2-1 (Batch 2022–2026 CSE)
INSERT INTO tblresult (RegNo, Semester, Subject, SubjectCode, Internals, Grade, Credits)
VALUES
('22CSE001', '2-1', 'Data Structures', 'CSE201', 23, 'A', 4),
('22CSE001', '2-1', 'Discrete Mathematics', 'MATH201', 21, 'B+', 3),
('22CSE001', '2-1', 'Digital Logic', 'CSE202', 25, 'A+', 4),

('22ECE001', '2-1', 'Signals & Systems', 'ECE201', 22, 'B+', 3),
('22ECE001', '2-1', 'Electronic Devices', 'ECE202', 20, 'B', 4),
('22ECE001', '2-1', 'Network Theory', 'ECE203', 24, 'A', 4);

-- Semester 3-1 (Batch 2021–2025 CSE, ECE, MECH, CIVIL)
INSERT INTO tblresult (RegNo, Semester, Subject, SubjectCode, Internals, Grade, Credits)
VALUES
('21CSE001', '3-1', 'Database Management Systems', 'CSE301', 24, 'A', 4),
('21CSE001', '3-1', 'Operating Systems', 'CSE302', 23, 'B+', 4),
('21CSE001', '3-1', 'Computer Networks', 'CSE303', 25, 'A+', 3),

('21ECE001', '3-1', 'Analog Communication', 'ECE301', 22, 'B+', 3),
('21ECE001', '3-1', 'Control Systems', 'ECE302', 21, 'B', 4),
('21ECE001', '3-1', 'Electromagnetic Theory', 'ECE303', 24, 'A', 4),

('21MECH001', '3-1', 'Machine Design', 'MECH301', 23, 'A', 4),
('21MECH001', '3-1', 'Heat Transfer', 'MECH302', 21, 'B+', 4),
('21MECH001', '3-1', 'Manufacturing Technology-II', 'MECH303', 22, 'B+', 3),

('21CIV001', '3-1', 'Concrete Technology', 'CIV301', 24, 'A', 3),
('21CIV001', '3-1', 'Hydrology', 'CIV302', 20, 'B', 4),
('21CIV001', '3-1', 'Structural Analysis-II', 'CIV303', 23, 'B+', 4);


-- --------------------------------------------------------
-- Table structure for table `tblsubjectcombination`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `tblsubjectcombination`;

-- --------------------------------------------------------
-- Table structure for table `tblfees` (flexible fee structure)
-- --------------------------------------------------------
DROP TABLE IF EXISTS `tblfees`;

CREATE TABLE `tblfees` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `RegNo` VARCHAR(50) CHARACTER SET latin1 NOT NULL,

  `Year` ENUM('1st Year','2nd Year','3rd Year','4th Year') NOT NULL,

  -- Tuition Fee
  `TuitionFeeAmount` DECIMAL(10,2) DEFAULT 0,
  `TuitionFeeRef` VARCHAR(100) DEFAULT NULL,

  -- Hostel Fee
  `HostelFeeAmount` DECIMAL(10,2) DEFAULT 0,
  `HostelFeeRef` VARCHAR(100) DEFAULT NULL,

  -- Bus Fee
  `BusFeeAmount` DECIMAL(10,2) DEFAULT 0,
  `BusFeeRef` VARCHAR(100) DEFAULT NULL,

  -- University Fee
  `UniversityFeeAmount` DECIMAL(10,2) DEFAULT 0,
  `UniversityFeeRef` VARCHAR(100) DEFAULT NULL,

  `CreatedAt` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

  FOREIGN KEY (`RegNo`) REFERENCES `tblstudents`(`RegNo`) ON DELETE CASCADE,

  -- 🚨 NEW UNIQUE CONSTRAINT
  UNIQUE KEY uq_fee_regno_year (`RegNo`, `Year`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


-- Example inserts
-- Student 1: Paid everything in 1st Year
INSERT INTO `tblfees` 
(RegNo, Year, TuitionFeeAmount, TuitionFeeRef, HostelFeeAmount, HostelFeeRef, BusFeeAmount, BusFeeRef, UniversityFeeAmount, UniversityFeeRef) 
VALUES
('22B81A0501', '1st Year', 
  20000, 'TU123, TU124', 
  15000, 'HO567', 
  3000,  'BU999', 
  5000,  'UNI321');

-- Student 2: Paid tuition + university, no hostel
INSERT INTO `tblfees` 
(RegNo, Year, TuitionFeeAmount, TuitionFeeRef, HostelFeeAmount, HostelFeeRef, BusFeeAmount, BusFeeRef, UniversityFeeAmount, UniversityFeeRef) 
VALUES
('22B81A0502', '2nd Year', 
  22000, 'TU567', 
  0, NULL, 
  2500, 'BU1001', 
  6000, 'UNI876');

-- Student 3: Hostel + tuition only
INSERT INTO `tblfees` 
(RegNo, Year, TuitionFeeAmount, TuitionFeeRef, HostelFeeAmount, HostelFeeRef, BusFeeAmount, BusFeeRef, UniversityFeeAmount, UniversityFeeRef) 
VALUES
('22B81A0503', '3rd Year', 
  18000, 'TU890', 
  20000, 'HO432, HO433', 
  0, NULL, 
  7000, 'UNI111');

-- Student 4: Paid only bus and university fees
INSERT INTO `tblfees` 
(RegNo, Year, TuitionFeeAmount, TuitionFeeRef, HostelFeeAmount, HostelFeeRef, BusFeeAmount, BusFeeRef, UniversityFeeAmount, UniversityFeeRef) 
VALUES
('22B81A0504', '4th Year', 
  0, NULL, 
  0, NULL, 
  3500, 'BU222', 
  5500, 'UNI909');

CREATE TABLE enquiries (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    course VARCHAR(50) NOT NULL,
    branch VARCHAR(100) NOT NULL,
    message TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
-- --------------------------------------------------------
-- Table structure for table `tblattendance`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `tblattendance`;
CREATE TABLE `tblattendance` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `RegNo` VARCHAR(50) NOT NULL,          -- FK to tblstudents.RegNo
  `Semester` VARCHAR(10) NOT NULL,
  `AttendanceDate` DATE NOT NULL,
  `TotalPeriods` INT NOT NULL DEFAULT 0,
  `PresentPeriods` INT NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY uq_attendance_regno_semester (`RegNo`, `Semester`),
  CONSTRAINT fk_att_student FOREIGN KEY (`RegNo`) REFERENCES `tblstudents`(`RegNo`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS tblstaffsubjects;

CREATE TABLE `tblstaffsubjects` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `SubjectId` INT NOT NULL,
  `StaffId` INT NOT NULL,
  `AssignedDate` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_subject (SubjectId),  -- one subject → one staff
  FOREIGN KEY (SubjectId) REFERENCES tblsubjects(id) ON DELETE CASCADE,
  FOREIGN KEY (StaffId) REFERENCES tblstaff(StaffId) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

COMMIT;