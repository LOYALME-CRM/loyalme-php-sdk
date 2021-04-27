# LoyalMe SDK library  
##### Version: 1.0 

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
#### Create category
###### code:  
    $category = $categoryObject->create(string $extCategoryId, string $name, int $parentExtCategoryId = null);  
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
#### Update category
###### code:  
    $category = $categoryObject->update(string $extCategoryId, string $name, int $parentExtCategoryId = null);  
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
#### Delete category
###### code:  
    $category = $categoryObject->delete(string $extCategoryId);  
###### <a id="#category"></a> result:
    {
      "message": "string",
      "code": 0,
      "status_code": 200 if ok
    }  