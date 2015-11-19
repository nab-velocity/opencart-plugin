Velocity OpenCart Module Installation Documentation 

1.	Configuration Requirement: Opencart site Version 2.0.2.0 or above version must be required for our velocity payment module installation.
	
2.	Download velocity Opencart Module by clicking on Download zip button on the right bottom of this page.

3.	Installation & Configuration of Module from Admin Panel:
	  Unzip module code and open "velocity_opencart" folder and upload code on your server as per the directory structure matched with your Opencart dircetory structure, after upload the module code on server open browser login admin panel and click on 'Extensions' Menu option then click on 'payments' our velocity payment module listed here.

Show the list of all payment module listed after succesfull upload your velocity module is also listed.

Click on module install/uninstall button of the module and after successfull installation click on edit button for configure the module save velocity credential and also enbale/disable module on Testing mode or production mode.

VELOCITY CREDENTIAL DETAILS
1.	Identity Token: - This is security token provided by velocity to merchant.
2.	WorkFlowId/ServiceId: - This is servuce id provided by velocity to merchant.
3.	ApplicationProfileId: - This is Application id provided by velocity to merchant.
4.	MerchantProfileId: - This is Merchant id provided by velocity to merchant.
5.	Test Mode :- This is for test the module, if select radiobox for test mode or production.

For Refund option at admin side first open sales->orders from left menu and all order display here then click on view order icon and show the differnent tabs select action tab shows Velocity Refund Option for refund amount directly from gateway.

For update/uninstall the velocity module of Opencart click on 'Extensions' Menu option then click on 'payments' select module and edit for change the configuration and for uninstallation remove the module but code not remove from sever.

4.  We have saved the raw request and response objects in &lt;prefix&gt;_velocity_transactions table.