<?php
class Tromvia_Nhapsanpham_Adminhtml_NhapsanphamController
	extends Mage_Adminhtml_Controller_Action
{
 	protected function _initAction()
    {
    	//set active menu, title
        $this->_title($this->__('Nhập hàng'))
             ->loadLayout()
             ->_setActiveMenu('tromvia/nhapsanpham_server');

        return $this;
    }
	
	public function indexAction()
	{ 
		$this->_initAction()
             ->_title($this->__('Nhập Hàng'))
             ->_addBreadcrumb($this->__('Nhập Hàng'), $this->__('Nhập Hàng')); 
		$this->renderLayout();
	}
	
	public function executeAction()
	{
		$request = $this->getRequest();		
		$param = $request->getParam('param');
		$nhapsp=Mage::getModel('nhapsanpham/service_nhapsanpham');
		$nhapsp->setInputFile($param);
		$nhapsp->setProductFile($param);
		$nhapsp->run();		
		
		$this->getResponse()->setBody($output);
	}
	
	public function executeImagesAction()
	{
		$request = $this->getRequest();		
		$param = $request->getParam('param');
		$anhSp=Mage::getModel('nhapsanpham/service_anhsanpham');
		$anhSp->setInputFile($param);		
		$anhSp->run();
	}
    public function generateSkuAction()
	{
		$request = $this->getRequest();
		$param = $request->getParam('param');
		$anhSp=Mage::getModel('nhapsanpham/service_nhapsanpham');
		$anhSp->setInputFile($param);
		$anhSp->generateSku();
	}
	public function uploadProductCsvAction(){				
		// Store input_csv upload
            if(isset($_FILES['csvfile']['name']) && $_FILES['csvfile']['name'] != '') {
                $targetPath = Mage::helper('nhapsanpham')->_getUploadFolder(); //desitnation directory  
                //upload file
                $result = $this->uploadFile($targetPath);
                if($result['file']) {
                    $data['csvfile'] = $result['file'];
                }                
            } else {  
                //if you are using an image field type, (image is set in addField)
                if(isset($data['image']['delete']) && $data['image']['delete'] == 1) {                    
                    $data['image'] = '';
                } else {
                    unset($data['image']);
                }
            }
			$this->_redirect('*/*/');
	}
	/**
     * Upload new file
     *
     * @param string $targetPath Target directory
     * @throws Mage_Core_Exception
     * @return array File info Array
     */
	public function uploadFile($targetPath)
    {		
        try {    				
            //$FileName = $_FILES['csvfile']['name']; //file name     
            $FileName = date('H', time())."gio".date('i', time())."phut-"."ngay-".date('d-m-Y', time()).".csv"; //file name     
                 
            $uploader = new Mage_Core_Model_File_Uploader('csvfile'); //load class
            $uploader->setAllowedExtensions(array('jpg','jpeg','gif','png','csv'));//Allowed extension for file
            $uploader->setAllowCreateFolders(true); //for creating the directory if not exists
            $uploader->setAllowRenameFiles(true); //if true, uploaded file's name will be changed, if file with the same name already exists directory.
            $uploader->setFilesDispersion(false);

            $result = $uploader->save($targetPath, $FileName); //save the file on the specified path
            if (!$result) {
                 Mage::throwException( Mage::helper('nhapsanpham')->__('Cannot upload file.') );
            }

        } catch (Exception $e) {
            $this->_getSession()->addException($e, $e->getMessage());
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('nhapsanpham')->__('Có sự cố khi upload file'));
            return;
        }
		Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('nhapsanpham')->__('Đã upload xong file import'));

        $nhapSp=Mage::getModel('nhapsanpham/service_nhapsanpham');
        $nhapSp->setInputFile($FileName);
        $nhapSp->generateSku($FileName);
        return $result;
    }
}