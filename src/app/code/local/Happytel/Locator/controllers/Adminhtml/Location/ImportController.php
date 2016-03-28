<?php
/**
 * Controller for location Attributes Management
 */
class Happytel_Locator_Adminhtml_Location_ImportController
    extends Mage_Adminhtml_Controller_Action
{
    /**
     * Load layout, set breadcrumbs
     *
     * @return Ak_Locator_Adminhtml_Location_AttributeController
     */
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('happytel_locator/location')
            ->_addBreadcrumb(
                Mage::helper('happytel_locator')->__('Location'),
                Mage::helper('happytel_locator')->__('Location'))
            ->_addBreadcrumb(
                Mage::helper('happytel_locator')->__('Manage Location Attributes'),
                Mage::helper('happytel_locator')->__('Manage Location Attributes'));
        return $this;
    }

    /**
     * Retrieve location attribute object
     *
     * @return Ak_Locator_Model_Attribute
     */
    protected function _initAttribute()
    {
        $attribute = Mage::getModel('ak_locator/attribute');
        return $attribute;
    }

    /**
     * Attributes grid
     *
     */
    public function indexAction()
    {
        $this->_title($this->__('Manage Locations'));
        $this->_initAction()
            ->renderLayout();
    }

    /**
     * Validate attribute action
     *
     */
    public function validateAction()
    {
        $response = new Varien_Object();
        $response->setError(false);
        $attributeId        = $this->getRequest()->getParam('attribute_id');
        if (!$attributeId) {
            $attributeCode      = $this->getRequest()->getParam('attribute_code');
            $attributeObject    = $this->_initAttribute()
                ->loadByCode($this->_getEntityType()->getId(), $attributeCode);
            if ($attributeObject->getId()) {
                $this->_getSession()->addError(
                    Mage::helper('ak_locator')->__('Attribute with the same code already exists')
                );

                $this->_initLayoutMessages('adminhtml/session');
                $response->setError(true);
                $response->setMessage($this->getLayout()->getMessagesBlock()->getGroupedHtml());
            }
        }
        $this->getResponse()->setBody($response->toJson());
    }

    /**
     * URL redirect save action
     */
    public function saveAction()
    {
        $data = $this->getRequest()->getPost();
        if (!$data) {
            $this->_redirect('*/locator/index');
            return;
        }

        try {

            if (isset($_FILES['import']['name'])) {
                $path = Mage::getBaseDir(). DS . 'var'. DS . 'locator'; //Mage::getBaseDir().DS.'customer_documents'.DS; //desitnation directory
                $fname = $_FILES['import']['name']; //file name
                $uploader = new Varien_File_Uploader('import'); //load class
                $uploader->setAllowedExtensions(array('txt', 'csv')); //Allowed extension for file
                $uploader->setAllowCreateFolders(true); //for creating the directory if not exists
                $uploader->setAllowRenameFiles(false); //if true, uploaded file's name will be changed, if file with the same name already exists directory.
                $uploader->setFilesDispersion(false);
                $result = $uploader->save($path, $fname); //save the file on the specified path
                $file = $result['path'].DS.$result['file'];
                $info = pathinfo($file);
                $import = Mage::getModel('happytel_locator/import');

                if ($info['extension'] == 'txt') {
                    $handle = fopen($file, "r");
                    if ($handle) {
                        $i = 0;
                        while (($line = fgets($handle)) !== false) {
                            $i++;
                            if ($i == 1) {
                                continue;
                            }
                            // process the line read.
                            $line = ltrim($line, "['");
                            $line = rtrim($line, " ");
                            $line = rtrim($line, "\n\r\t\0\x0B");
                            $line = trim($line, " ");
                            $line = rtrim($line, "']");
                            $line = str_replace("', '", "','", $line);
                            $rows[] = explode("','", $line);
                        }

                        fclose($handle);
                    } else {
                        // error opening the file.
                    }

                    Mage::log(count($rows),Zend_Log::DEBUG, 'store_import.log');
                    //$i = 0;
                    foreach ($rows as $row) {
                        //$i++;
                        Mage::log($i,Zend_Log::DEBUG, 'store_import.log');
                        if (isset($row[0])) $dataRow['branch_no'] = $row[0];
                        if (isset($row[1]))  $dataRow['title'] = $row[1];
                        if (isset($row[2])) {
                            $companyLongLatAddress = $row[2];
                            $companyLongLatAddress = explode("'", $companyLongLatAddress);
                            $dataRow['company_name'] = trim($companyLongLatAddress[0]);
                            $latLong = trim($companyLongLatAddress[1]," ");
                            $latLong = trim($latLong, ",");
                            $latLong = explode(",", $latLong);
                            $dataRow['latitude'] = $latLong[0];
                            $dataRow['longitude'] = $latLong[1];

                            $address = $companyLongLatAddress[2];
                            $dataRow['address'] = str_replace('<br>', '', $address);
                            $address = explode('<br>', $address);
                            $dataRow['dependent_locality'] = trim($address[0]);
                            $dataRow['locality'] = trim($address[1]);
                            $localityPostcode = end($address);
                            if (strstr($localityPostcode, 'New Zealand')) {
                                $localityPostcode = str_replace('New Zealand', '', $localityPostcode);
                                $localityPostcode = trim($localityPostcode, '\t\r\n\0\x0B');
                                $localityPostcode = trim($localityPostcode);
                                $localityPostcode = trim($localityPostcode, ',');
                                $dataRow['country'] = 'New Zealand';
                                $localityPostcode = explode(" ", $localityPostcode);
                                $dataRow['postal_code'] = end($localityPostcode);
                                array_pop($localityPostcode);   //remove the last item - postal code
                                $dataRow['locality'] = implode(" ", $localityPostcode);

                            } else {
                                $localityPostcode = explode(" ", $localityPostcode);

                                $dataRow['postal_code'] = end($localityPostcode);
                                array_pop($localityPostcode); //remove the last item - postal code
                                $dataRow['administrative_area'] = array_pop($localityPostcode); ////remove the last item - region
                                $dataRow['locality'] = array_pop($localityPostcode);
                            }
                        }

                        if (isset($row[3])) $phone = strip_tags($row[3]);
                        $dataRow['phone'] = $phone;

                        $tradingHours = strip_tags($row[4]);
                        $tradingHours = str_replace('[Mon]', "", $tradingHours);
                        $tradingHours = str_replace(array('[Mon]', '[Tue]', '[Wed]', '[Thu]', '[Fri]', '[Sat]', '[Sun]', '[Public Holidays]'), ",", $tradingHours);
                        $tradingHours = explode(",", $tradingHours);
                        if (isset($tradingHours[0])) $dataRow['monday'] = trim($tradingHours[0]);
                        if (isset($tradingHours[1])) $dataRow['tuesday'] = trim($tradingHours[1]);
                        if (isset($tradingHours[2])) $dataRow['wednesday'] = trim($tradingHours[2]);
                        if (isset($tradingHours[3])) $dataRow['thursday'] = trim($tradingHours[3]);
                        if (isset($tradingHours[4])) $dataRow['friday'] = trim($tradingHours[4]);
                        if (isset($tradingHours[5])) $dataRow['saturday'] = trim($tradingHours[5]);
                        if (isset($tradingHours[6])) $dataRow['sunday'] = trim($tradingHours[6]);

                        if (isset($row[5])) {
                            $dataRow['google_map_iframes'] = $row[5];
                        }

                        $import->saveFromCsv($dataRow);
                    }
                }

                if ($info['extension'] == 'csv') {
                    $import->run($file);
                }
            }
            $this->_getSession()->addSuccess($this->__('Import location successful.'));
            $this->_redirect('*/locator/index');
            return;
        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
            $this->_redirect('admin/locator/index');
            return;
        } catch (Exception $e) {
            Mage::logException($e);
            $this->_getSession()->addError($this->__('An error occurred while saving locations.'));
            $this->_redirect('*/locator/index');
            return;
        }
    }

}
