# LoyalMe SDK library  
##### Version: 1.0 

Calling the **get ()** method receives the object data while simultaneously updating it in the database with data from the query parameters

One method is used to create, update and retrieve object data - **get ()**.

A new object is created if it is impossible to find the object using the data specified in the parameters, otherwise the data in the database is updated and the object data is written in the object properties.


## Category:  
___
### How to create the object  

`$categoryObject = new Category($connection, Category $parentCategory);`

#### Get category

###### code:
    try {
        $category = $categoryObject->get(
            string $extId,
            string $nameOfCategory,
            $parentCategory
        );
    } catch (CategoryException $e) {
        print_r($e->getErrorData);
        print_r($e->getCode);
        print_r($e->getErrorMessage);
    }
      
###### result:
    Category {
        "parentCategory": ?Category
        "attributes": [
            "id": int
            "name": string
            "parent_id": ?int
            "external_id": string
        ]
        "_connection": Connection
    }

#### Parameters' types

**string** extId _required*_    
**string** nameOfCategory _required*_   
**Category** parentCategory
    
## Product

### How to create the object  

`$productObject = new Product($connection);`

#### Get product

###### code
    try{
        $product = $productObject->get(
                int $extItemId = null,
                string $barcode = null,
                string $title = null,
                float $price = null,
                string $photo = null,
                int $isActive = 1,
                int $typeId = 1,
                float $accrualRate = 1,
                array $categories = [],
                array $aliases = [],
                array $customFields = []
            );
        } catch (ProductException $e) {
                var_dump($e->getErrorData);
                var_dump($e->getCode);
                var_dump($e->getErrorMessage);
        }
        
        //Delete product
        $productObject->delete($extItemId);
        $productObject->delete(null, $barcode);

###### result:
    Product {
        "attributes": [
            "id": int
            "points": int
            "ext_item_id": int
            "title": string
            "barcode": string
            "price": float
            "is_active": int
            "type_id": int
            "point_id": int
            "accrual_rate": int
            "ext_photo_url": string
            "categories": array
            "aliases": array
        ]
        "_connection": Connection
    }

#### Parameters' types

**int** extItemId _required*_    
**string** barcode  
**string** title _required*_   
**float** price _required*_    
**string** photo    
**int** isActive  
**int** typeId  
**float** accrualRate  
**array** categories _required*_ - array of Categories objects  
**array** aliases  
**array** customFields  

`$productAttributes = $productObject->attributes; //array`

**Also you can refer to any property of the object directly:**

`$productAttributes = $productObject->id;`

