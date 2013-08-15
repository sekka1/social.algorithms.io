
begin

START n=node(*)
WHERE n.node_db_label! = "PersonGUID"
SET n :PersonGUID
RETURN count(n);

START n=node(*)
WHERE n.node_db_label! = "Education"
SET n :Education
RETURN count(n);

START n=node(*)
WHERE n.node_db_label! = "Institution"
SET n :Institution
RETURN count(n);

START n=node(*)
WHERE n.node_db_label! = "Degree"
SET n :Degree
RETURN count(n);

START n=node(*)
WHERE n.node_db_label! = "Employment"
SET n :Employment
RETURN count(n);

START n=node(*)
WHERE n.node_db_label! = "EmploymentTitle"
SET n :EmploymentTitle
RETURN count(n);

START n=node(*)
WHERE n.node_db_label! = "EmploymentFirm"
SET n :EmploymentFirm
RETURN count(n);

START n=node(*)
WHERE n.node_db_label! = "Funding"
SET n :Funding
RETURN count(n);

START n=node(*)
WHERE n.node_db_label! = "Investors"
SET n :Investors
RETURN count(n);

commit
exit


// Angel List

START n=node(*)
WHERE n.node_db_label! = "PersonGUID"
SET n :PersonGUID
RETURN count(n);

START n=node(*)
WHERE n.node_db_label! = "AlRoles"
SET n :AlRoles
RETURN count(n);

START n=node(*)
WHERE n.node_db_label! = "EmploymentFirm"
SET n :EmploymentFirm
RETURN count(n);

START n=node(*)
WHERE n.node_db_label! = "EmploymentRole"
SET n :EmploymentRole
RETURN count(n);

START n=node(*)
WHERE n.node_db_label! = "Skill"
SET n :Skill
RETURN count(n);

START n=node(*)
WHERE n.node_db_label! = "Market"
SET n :Market
RETURN count(n);

START n=node(*)
WHERE n.node_db_label! = "Location"
SET n :Location
RETURN count(n);

START n=node(*)
WHERE n.node_db_label! = "CompanyType"
SET n :CompanyType
RETURN count(n);

START n=node(*)
WHERE n.node_db_label! = "FundRaising"
SET n :FundRaising
RETURN count(n);

