
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