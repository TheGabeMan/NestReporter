/*
    Laatste 24u aan rawdata. Vervolgens opnieuw gesorteerd zodat oudste 
    record als eerste wordt weergegeven.
*/

SELECT * FROM 
    (
        SELECT * FROM  `rawdata` 
        WHERE `rawdata`.`timestamp` > DATE_SUB(NOW(), INTERVAL 1 DAY) 
        ORDER BY  `rawdata`.`timestamp` 
    ) AS ttbl 
    ORDER BY `timestamp` ASC

/*
    Laatste week aan rawdata. Vervolgens opnieuw gesorteerd zodat oudste 
    record als eerste wordt weergegeven.
*/

SELECT * FROM 
    (
        SELECT * FROM  `rawdata` 
        WHERE `rawdata`.`timestamp` > DATE_SUB(NOW(), INTERVAL 1 WEEK) 
        ORDER BY  `rawdata`.`timestamp` 
    ) AS ttbl 
    ORDER BY `timestamp` ASC



