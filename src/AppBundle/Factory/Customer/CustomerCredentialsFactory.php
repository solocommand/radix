<?php

namespace AppBundle\Factory\Customer;

use AppBundle\Factory\AbstractModelFactory;
use AppBundle\Factory\Error;
use AppBundle\Factory\ValidationFactoryInterface;
use As3\Modlr\Models\AbstractModel;
use As3\Modlr\Models\Embed;
use As3\Modlr\Models\Model;
use As3\Modlr\Store\Store;

/**
 * Factory for customer credentials
 *
 * @author  Jacob Bare <jacob.bare@gmail.com>
 */
class CustomerCredentialsFactory extends AbstractModelFactory implements ValidationFactoryInterface
{
    /**
     * @var CustomerCredentialsPasswordFactory
     */
    private $password;

    /**
     * @var CustomerCredentialsSocialFactory
     */
    private $social;

    /**
     * @param   CustomerCredentialsPasswordFactory  $password
     * @param   CustomerCredentialsSocialFactory    $social
     */
    public function __construct(Store $store, CustomerCredentialsPasswordFactory $password, CustomerCredentialsSocialFactory $social)
    {
        parent::__construct($store);
        $this->password = $password;
        $this->social   = $social;
    }

    /**
     * Applies credential details to the password credentials models.
     *
     * @param   Embed       $credentials
     * @param   string      $clearPassword  The cleartext (unencoded) password.
     * @param   string      $mechanism
     * @param   string|null $username
     * @return  Embed
     */
    public function applyPasswordCredential(Embed $credentials, $clearPassword, $mechanism = 'platform', $username = null)
    {
        if (false === $this->supportsEmbed($credentials)) {
            $this->getUnsupportedError()->throwException();
        }

        $credential = $credentials->createEmbedFor('password');
        $this->getPasswordFactory()->apply($credential, $clearPassword, $mechanism, $username);
        $credentials->set('password', $credential);
        return $credentials;
    }

    /**
     * {@interitdoc}
     */
    public function canSave(AbstractModel $credentials)
    {
        if (false === $this->supportsEmbed($credentials)) {
            // Ensure this is the correct embed model.
            return $this->getUnsupportedError();
        }

        $this->preValidate($credentials);

        if (null !== $password = $credentials->get('password')) {
            // Ensure password credential can be saved.
            if (true !== $result = $this->getPasswordFactory()->canSave($password)) {
                return $result;
            }
        }
        return true;
    }

    /**
     * @return  CustomerCredentialsPasswordFactory
     */
    public function getPasswordFactory()
    {
        return $this->password;
    }

    /**
     * @return  CustomerCredentialsSocialFactory
     */
    public function getSocialFactory()
    {
        return $this->social;
    }

    /**
     * {@interitdoc}
     */
    public function postValidate(AbstractModel $credentials)
    {
        if (null !== $password = $credentials->get('password')) {
            $this->getPasswordFactory()->postValidate($password);
        }
    }

    /**
     * {@interitdoc}
     */
    public function preValidate(AbstractModel $credentials)
    {
    }

    /**
     * Gets the unsupported embed type error.
     *
     * @return  Error
     */
    private function getUnsupportedError()
    {
        return new Error('The provided embed model is not supported. Expected an instance of `customer-credentials`');
    }

    /**
     * Determines if the embed is supported.
     *
     * @param   Embed   $password
     * @return  bool
     */
    private function supportsEmbed(Embed $password)
    {
        return 'customer-credentials' === $password->getName();
    }
}