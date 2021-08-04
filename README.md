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
    
##Delivery Method

### How to create the object  

`$categoryObject = new DeliveryMethod(Connection $connection)`

#### Get method
###### code:
    try {  
        $deliveryMethod = New DeliveryMethod(makeConnection());
        $deliveryMethod->get('English name', 'slug');
    } catch (DeliveryMethodException $e) {
        
    }
      
### Delete method 
  
###### code:
    try {  
        $deliveryMethod = New DeliveryMethod(makeConnection());
        $deliveryMethod->delete('slug');
    } catch (DeliveryMethodException $e) {
        
    }
 
 ##Order Status
 
 ### How to create the object  
 
 `$categoryObject = new OrderStatus(Connection $connection)`
 
 #### Get method
 ###### code:
     try {  
         $deliveryMethod = New OrderStatus(makeConnection());
         $deliveryMethod->get('English name', 'slug');
     } catch (OrderStatusException $e) {
         
     }
       
 ### Delete method 
   
 ###### code:
     try {  
         $deliveryMethod = New OrderStatus(makeConnection());
         $deliveryMethod->delete('slug');
     } catch (OrderStatusException $e) {
         
     }   
 
 ##Payment Status
 
 ### How to create the object  
 
 `$categoryObject = new PaymentStatus(Connection $connection)`
 
 #### Get method
 ###### code:
     try {  
         $deliveryMethod = New PaymentStatus(makeConnection());
         $deliveryMethod->get('English name', 'slug');
     } catch (PaymentStatusException $e) {
         
     }
       
 ### Delete method 
   
 ###### code:
     try {  
         $deliveryMethod = New PaymentStatus(makeConnection());
         $deliveryMethod->delete('slug');
     } catch (PaymentStatusException $e) {
         
     }   