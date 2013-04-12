==Title==
FatchipPAYONE

==Author==
Fatchip GmbH

==Prefix==
fc

==Version==
1.3.1b4_2297

==Link==
http://www.payone.de
http://www.fatchip.de

==Mail==
<a href="mailto:kontakt@fatchip.de">kontakt@fatchip.de</a>

==Description==
The PAYONE-FinanceGate-Module offers more than 20 payment methods for your OXID-Shopsystem. Beside commonly used payment methods as 
Paypal, Sofortueberweisung.de or eight Creditcards there is also the possibility to offer secure paying via bill or debit.

==Extend==
*someClass
--some method

==Installation==
Notice: You can find the german guide in readme.txt file at the beginning of the folder structure.

1. Extract the module-package.
2. Copy the content of the folder "copy_this" into your shop root-folder (where config.inc.php lies).
3. Transfer the content of the folder "changed_full" belonging to your shop-version into the shop ( if there have been changes in the shop refering to the files listed in "changed_full you have to merge these files manually ).
   - If you use Oxid 4.2.X use "changed_full_42"
   - If you use Oxid 4.3.X use "changed_full_43"
   - If you use Oxid 4.4.X use "changed_full_44"
   - If you use Oxid 4.5.X use "changed_full_45"
   - If you use Oxid 4.6.X use "changed_full_46"
   - Not needed in Oxid 4.7 and 5.0
4. Execute the installation-/update-script - For doing that, call in your browser: http://->YOUR-SHOP<-/fcpoSetup.php. Afterwards you can delete fcpoSetup.php.
5. In the admin-interface of OXID-Shop you go to Service->Tools and press the button "Update Views now".
6. If you are using Oxid 4.6.0 or higher go to Extensions->Modules, select the "PAYONE FinanceGate" extension and press the "Activate" Button in the "Overview" tab.
If you are Oxid version older than 4.6.0 set module entries by going to Master Settings->Core Settings->System->Modules and add or merge these entries:
oxbasketitem => fcPayOne/core/fcPayOneBasketitem
oxorder => fcPayOne/core/fcPayOneOrder
oxorderarticle => fcPayOne/core/fcPayOneOrderarticle
oxpayment => fcPayOne/core/fcPayOnePayment
oxpaymentgateway => fcPayOne/core/fcPayOnePaymentgateway
oxuser => fcPayOne/core/fcPayOneUser
payment => fcPayOne/views/fcPayOnePaymentView
roles_bemain => fcPayOne/admin/fcPayOneRolesBeMain
CAUTION: If one of the classes needed to be overwritten still uses another module which overwrites the class, you have to add the module entry by using the &-sign
e. g.:
oxorder => your/module/order&fcPayOne/core/fcPayOneOrder
7. Next you need to deposit a transaction url in the PAYONE-Webinterface at Konfiguration -> Zahlungsportale -> YOUR_PORTAL -> Erweitert -> TransactionStatus URL  .
The URL has to look like this:
http://->YOUR_SHOP<-/modules/fcPayOne/status.php
8. Empty "tmp" folder.
9. There is a new menu item in the OXID-Interface named PAYONE. Here you can set your merchant connect data.

