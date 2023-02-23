<?php

/* Report 9 - Advertising Campaign Analysis   */

$SQL_ADVERT_ANALYSIS = "
SELECT PID, ProductName,
			 SoldDuringCampaign, SoldOutsideCampaign,
			 SoldDuringCampaign - SoldOutsideCampaign AS Diff
	FROM (
				SELECT PID, ProductName,
							 (SELECT SUM(Quantity)
									FROM Sale, Discount
								 WHERE Sale.PID = P.PID
									 AND Sale.PID = Discount.PID
									 AND Date(Sale.SaleDate) = Date(Discount.DiscountDate)) SoldDuringCampaign,
							 (SELECT SUM(Quantity)
									FROM Sale
								 WHERE Sale.PID = P.PID
									 AND NOT EXISTS(SELECT * FROM Discount
																	 WHERE PID = Sale.PID
																		 AND Date(Discount.DiscountDate) = Sale.SaleDate)) SoldOutsideCampaign
					FROM Product P) As Results
ORDER BY Diff DESC
";


/* Manage Holidays */

$SQL_CITY_LIST ="
SELECT CityID, CityName, StateName
FROM City, State
WHERE City.StateCode = State.StateCode
ORDER BY CityName
";

function getCurrentPop($cityID){
	$SQL_CURRENT_POP ="
	SELECT Population
	FROM City
	WHERE CityID = '$cityID'
	";

	return $SQL_CURRENT_POP;
};

function updatePop($UpdatePop,$CityToUpdate){
	$SQL_UPDATE_POP ="
	UPDATE City
	SET Population = $UpdatePop
	WHERE CityID = $CityToUpdate
	";
};

?>
