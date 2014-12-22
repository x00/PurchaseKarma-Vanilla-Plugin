<?php if (!defined('APPLICATION')) exit();
// Define the plugin:
$PluginInfo['PurchaseKarma'] = array(
   'Name' => 'Purchase Karma',
   'Description' => "Allows users to purchace Karma",
   'Version' => '0.1.2b',
   'RequiredPlugins' => array('MarketPlace' => '0.1.9b','KarmaBank' =>'0.9.7.3b'),
   'RequiredApplications' => array('Vanilla' => '2.1'),
   'Author' => 'Paul Thomas',
   'AuthorEmail' => 'dt01pqt_pt@yahoo.com',
   'AuthorUrl' => 'http://www.vanillaforums.org/profile/x00'
);

/*
* # Purchase Karma #
*
* ### About ###
* Allows users to purchace Karma.
* 
* ### Sponsor ###
* Special thanks to oboyledk for making this happen.
*/

class PurchaseKarma extends Gdn_Plugin {
    
    public static function PreConditions($UserID,$Product){
        if(is_string($Product->EnabledGateways))
            $Product->EnabledGateways=Gdn_Format::Unserialize($Product->EnabledGateways);
        $Product->EnabledGateways['Karma'] = 0;// no point it buying Karma with Karma
        return array('status'=>'pass');
    }
    
    public static function AddKarma($UserID,$Product,$TransactionID){
        $Quantity=1;
        $VariableMeta=MarketTransaction::GetTransactionMeta($TransactionID);
        $Meta=Gdn_Format::Unserialize($Product->Meta);
        $DefaultQuantity = GetValue('Quantity',$Meta,1);
        $DefaultQuantity = ctype_digit($DefaultQuantity)?$DefaultQuantity:1;
        $Quantity=GetValue('Quantity',$VariableMeta,$DefaultQuantity);
        $KarmaUnit=GetValue('KarmaUnit',$Meta,1);
        $Value=$KarmaUnit*$Quantity;
        $User =  Gdn::UserModel()->GetID($UserID);
        $Type=sprintf(T('%s+purchaced+%01.2f+Karma+in+Store'),$User->Name,$Value);
        $KarmaBank = new KarmaBankModel($UserID);
        $KarmaBank->Transaction($Type,$Value,$Value);
        return array('status'=>'success');
        
    }
    
    public function MarketPlace_LoadMarketPlace_Handler($Sender){
        $Options = array(
            'Meta'=>array('Quantity','KarmaUnit'),
            'RequiredMeta'=>array('Quantity','KarmaUnit'),
            'ValidateMeta'=>array('Quantity'=>'Integer', 'KarmaUnit'=>'Integer'),
            'VariableMeta'=>array('Quantity'),
            'ReturnComplete'=>'/profile/karmabank/'.(Gdn::Session()->UserID).'/'.rawurlencode(Gdn::Session()->User->Name)
        );
        $Sender->RegisterProductType('PurchaseKarma','Allows users to purchase Karma',$Options,'PurchaseKarma::PreConditions','PurchaseKarma::AddKarma');
    }
    
    public function Setup() {

        $this->Structure();
    }
    
    public function Structure(){
        
    }
    
    
    

}
