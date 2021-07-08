# LoyalMe SDK library  
##### Version: 1.0 

Calling the **get ()** method receives the object data while simultaneously updating it in the database with data from the query parameters

One method is used to create, update and retrieve object data - **get ()**.

A new object is created if it is impossible to find the object using the data specified in the parameters, otherwise the data in the database is updated and the object data is written in the object properties.


##Category:  
___
### How to create the object  

`$categoryObject = new Category(Connection $connection)`

#### Get category

###### code:
    try {
        $category = $categoryObject->get(string $extId, string $nameOfCategory, $parentCategory);
    } catch (CategoryException $e) {
        print_r($e->getErrorData);
        print_r($e->getCode);
        print_r($e->getErrorMessage);
    }
      
###### result:
    {
        "data": 
        {  
            "id": 0,  
            "name": "string",  
            "parent_id": 0,  
            "external_id": "string"  
        }  
    }