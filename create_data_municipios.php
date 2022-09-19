<?php

include 'config.php';

// PHP program to connect with
// localserver database
$user = $userdb;
$password = $pwddb;
$database = $dbdb;
$servername = $serverdb;

$mysqli = new mysqli(
    $servername,
    $user,
    $password,
    $database
);

if ($mysqli->connect_error) {
    die('Connect Error (' .
        $mysqli->connect_errno . ') ' .
        $mysqli->connect_error);
}

// SQL query to select data
// from database
$sql2 = "
with total_provincias as
(
select
	`year`,
	cpro, 
	sum(case when `year` = 2020 then dp.n_obs else 0 end) n_alquileres_provincia_2020
from
	data_provincias dp
where
	`type` = 'VC'
group by
	cpro,
	`year`),
total_comunidades as
(
select
	dp.`year`,
	c.cca, 
	sum(case when `year` = 2020 then dp.n_obs else 0 end) n_alquileres_comunidad_2020
from
	data_provincias dp
join provincias p on
	dp.cpro = p.CPRO
join comunidades c on
	p.CCA = c.CCA
where
	`type` = 'VC'
group by
	c.cca,
	`year`),
rent_sqm_median_provincias_2020 as (
select
	`year`,
	cpro,
	sum(case when `year` = 2020 then dp.rent_sqm_median else 0 end) rent_sqm_median_2020
from
	data_provincias dp
where
	`type` = 'VC'
	and `year` = 2020
group by
	`year`,
	cpro
),
data_2020_2019 as (
select
	m.LITMUN municipio,
	p.CPRO,
	p.LITPRO provincia,
	c.CCA cca,
	c.LITCA ca,
	sum(case when dm.`year` = 2020 then dm.n_obs else 0 end) n_alquileres_2020,
	sum(case when dm.`year` = 2020 then dm.rent_sqm_median else 0 end) rent_m2_median_2020,
	sum(case when dm.`year` = 2019 then dm.rent_sqm_median else 0 end) rent_m2_median_2019,
	sum(case when dm.`year` = 2020 then dm.rent_total_median else 0 end) rent_total_median_2020,
	sum(case when dm.`year` = 2020 then dm.size_sqm_median else 0 end) rent_size_median_2020
from
	data_municipios dm
join municipios m on
	dm.cumun = m.CUMUN
join provincias p on
	m.CPRO = p.CPRO
join comunidades c on
	p.CCA = c.CCA
where
	`type` = 'VC'
group by
	1,
	2,
	3,
	4,
	5
having
	n_alquileres_2020 > 0
order by
	5 desc)
select
	da.municipio,
	da.provincia,
	da.ca,
	max(da.n_alquileres_2020) n_alquileres_2020,
	max(da.n_alquileres_2020) / max(tp.n_alquileres_provincia_2020) p_alquileres_provincia_2020,
	max(da.n_alquileres_2020) / max(tc.n_alquileres_comunidad_2020) p_alquileres_comunidad_2020,
	max(da.rent_m2_median_2020) rent_m2_median_2020,
	(max(da.rent_m2_median_2020)-(rm.rent_sqm_median_2020)) / rm.rent_sqm_median_2020 diff_rent_sqm_median_provincia_2020,
	max(da.rent_m2_median_2019) rent_m2_median_2019,
	(max(da.rent_m2_median_2020)-max(da.rent_m2_median_2019))/ max(da.rent_m2_median_2019) diff_rent_m2_median_2019,
	max(rent_total_median_2020) rent_total_median_2020,
	max(rent_size_median_2020) rent_size_median_2020
from
	data_2020_2019 da
join total_provincias tp on
	da.CPRO = tp.cpro
join total_comunidades tc on
	da.CCA = tc.cca
join rent_sqm_median_provincias_2020 rm on 
	da.cpro = rm.cpro
group by
	1,
	2,
	3
order by
	da.n_alquileres_2020 desc";
$result = $mysqli->query($sql2);


$array = array();

	while ($row = $result->fetch_array()) {

     $row_array['municipio'] = $row['municipio'];
     $row_array['provincia'] = $row['provincia'];
     $row_array['ca'] = $row['ca'];
     $row_array['n_alquileres_2020'] = $row['n_alquileres_2020'];
     $row_array['p_alquileres_provincia_2020'] = $row['p_alquileres_provincia_2020'];
     $row_array['p_alquileres_comunidad_2020'] = $row['p_alquileres_comunidad_2020'];
     $row_array['rent_m2_median_2020'] = $row['rent_m2_median_2020'];
     $row_array['diff_rent_sqm_median_provincia_2020'] = $row['diff_rent_sqm_median_provincia_2020'];
     $row_array['rent_total_median_2020'] = $row['rent_total_median_2020'];
     $row_array['rent_size_median_2020'] = $row['rent_size_median_2020'];
     $row_array['diff_rent_m2_median_2019'] = $row['diff_rent_m2_median_2019'];
     

     array_push($array, $row_array);
	}


// Creating a dynamic JSON file
$file = "./data/data_vc.json";

// Converting data into JSON and putting
// into the file
// Checking for its creation
if (file_put_contents(
    $file,
    json_encode($array)
))
    echo ("File created");
else
    echo ("Failed");

// Closing the database
$mysqli->close();
