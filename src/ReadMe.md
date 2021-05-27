# LoyalMe SDK library  
##### Version: 1.0 

Calling the **get ()** method receives the object data while simultaneously updating it in the database with data from the query parameters

One method is used to create, update and retrieve object data - **get ()**.

A new object is created if it is impossible to find the object using the data specified in the parameters, otherwise the data in the database is updated and the object data is written in the object properties.


##Category:  
___
### How to create the object  

`$categoryObject = new Category($connection,$parentCategoryExtId)`

#### Get category

###### code:
  
    $category = $categoryObject->get(string $extId, string $nameOfCategory);
      
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
##Product

### How to create the object  

`$productObject = New Product($connection);`

#### Get product

###### code

`$product = $productObject(title, price, photoUrl, extItemId, barcode, isActive, typeId, accrualRate, categories, aliases, customFields)`

`$productAttributes = $productObject->attributes; //array`

**Also you can refer to any property of the object directly:**

`$productAttributes = $productObject->id`