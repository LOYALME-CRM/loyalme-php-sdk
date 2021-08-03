# LoyalMe SDK library  
##### Version: 1.0 

Calling the **get ()** method receives the object data while simultaneously updating it in the database with data from the query parameters

One method is used to create, update and retrieve object data - **get ()**.

A new object is created if it is impossible to find the object using the data specified in the parameters, otherwise the data in the database is updated and the object data is written in the object properties.


##Category:  
___
### How to create the object  

`$categoryObject = new Category($connection, Category $parentCategory)`

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
    
##Product

### How to create the object  

`$productObject = New Product($connection);`

#### Get product

###### code
    $productClass = new Product($connection)
    try{
        $product = $productClass->get(
                string $title,
                float $price,
                string $photo = null,
                int $extItemId = null,
                string $barcode = null,
                int $isActive = 1,
                int $typeId = 1,
                float $accrualRate = 1,
                array $categories = [],
                array $aliases = [],
                array $customFields = []
                );
        }catch (ProductException $e){
                    var_dump($e->getErrorData);
                    var_dump($e->getCode);
                    var_dump($e->getErrorMessage);
        }
        
        //Delete product
        $productClass->delete($extItemId);
        $productClass->delete(null, $barcode);

#### Result of get method
    {
      "data": [
        {
          "id": 0,
          "name": "string",
          "parent_id": 0,
          "external_id": "string"
        }
      ],
      "meta": {
        "pagination": {
          "total": 0,
          "count": 0,
          "per_page": 0,
          "current_page": 0,
          "total_pages": 0,
          "links": {
            "prev": "string",
            "next": "string"
          }
        }
      }
    }

#### Parameters' types

**string** title _required*_   
**float** price _required*_    
**string** photoUrl  
**string** extItemId  
**stirng** barcode  
**int** isActive  
**int** typeId  
**float** accrualRate  
**array** categories - _it's array of Categories objects_  
**array** aliases  
**array** customFields  

`$productAttributes = $productObject->attributes; //array`

**Also you can refer to any property of the object directly:**

`$productAttributes = $productObject->id`

