Admin Side
1. Request Records
  1.1. Item Request by User - - - - - - - -| 
  1.2. Borrow Request by User  - - - - - - |   All of these has the same functions that only shows the users requested and also if you needed to adjust the sql the tables 
  1.3. Returned Request by User - - - - - -|    

User Side
2. Request Records
  2.1. New Item Request - - - - - - |
  2.2. Borrow Item Request - - - - -| All of these has the functions of sending the request to the admin and to handle its request and also if you needed to adjust the sql the tables 
  2.3. Return Item Request - - - - -|

Scenarios: 
Admin Side:
1. Item Request by User: In this content the admin should see the user request for the new item that isn't still in the Inventory/Item Records
   and the admin has the choice to either approve/reject the request (if the admin approve the user request it should be inserted to the Item
   records table but the quantity of it is 0 because the user request is going to be requisited the item by the admin and if the admin rejected the request the admin will provided an explanation to the user on why he/she rejected the request)

2. Borrow Request: In this content the admin should see the user request to borrow an item that's in the Inventory/Item Records and the admin
   has the choice to either approve/reject the request (if the admin approve the user request it should be inserted to the Borrowed table and
   the status of the item especially for the Non-consumables should display the status of the item and if the admin rejected the request the admin
   will provided an explanation to the user on why he/she rejected the request)

3. Return Item Request: In this content is the same to the Borrow Request, The admin should see the user's request for the return item and should see the status of the item,
   for non-consumables,

User Side:
1. New Item Request: In this content the User will send a request form to the admin if the item that he/she wanted isn't in the Inventory/Item Records.
   Then the user should input the Item name, Item Category, Quantity, Date needed, Purpose, Additional Notes. After submitting the form the admin should receive this request
   
3. Borrow Item Request: In this content the User will send a request form to the admin if the item that he/she wanted isn't in the Inventory/Item Records.
   Then the user should select the Item name, Item Category, Quantity, Date needed, Return Date, Purpose, Additional Notes. After submitting the form the admin should receive this request
   
6. Return Item Request: In this content the User will send a request form to the admin if the item that he/she wanted isn't in the Inventory/Item Records.
   Then the user return what he/she borrowed on the Borrowed Form, the Item name, Item Category, Quantity to Return, Return Date, Condtion of Item, Additional Notes.
   After submitting the form the admin should receive this request, the Item especially the Non-consumables should return the status back to avaialble
