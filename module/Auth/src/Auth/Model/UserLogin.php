<?php

namespace Auth\Model;

use Zend\Form\Annotation;

/**
 * @Annotation\Hydrator("Zend\Stdlib\Hydrator\ObjectProperty")
 * @Annotation\Name("LoginForm")
 */
class UserLogin
{
    /**
     * @Annotation\Exclude()
     */
    protected $id;

    /**
     * @Annotation\Filter({"name":"StringTrim"})
     * @Annotation\Filter({"name":"StripTags"})
     * @Annotation\Validator({"name":"StringLength", "options":{"min":7, "max":255}})
     * @Annotation\Validator({"name":"Regex", "options":{"pattern":"([a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}$)"}})
     * @Annotation\Attributes({"type":"text","required":true,"pattern":"[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}$"})
     * @Annotation\Attributes({"id":"username"})
     * @Annotation\Type("Zend\Form\Element\Email")
     * @Annotation\Attributes({"class":"form-control"})
     */
    public $username;

    /**
     * @Annotation\Type("Zend\Form\Element\Password")
     * @Annotation\Required({"required":"true" })
     * @Annotation\Filter({"name":"StringTrim"})
     * @Annotation\Filter({"name":"StripTags"})
     * @Annotation\Attributes({"id":"password"})
     * @Annotation\Attributes({"class":"form-control"})
     */
    public $password;

    /**
     * @Annotation\Type("Zend\Form\Element\Checkbox")
     * @Annotation\Attributes({"type":"checkbox"})
     * @Annotation\Attributes({"id":"rememberme"})
     */
    public $rememberme;

    /**
     * @Annotation\Type("Zend\Form\Element\Submit")
     * @Annotation\Attributes({"value":"Submit"})
     * @Annotation\Attributes({"class":"btn btn-primary"})
     */
    public $submit;
}
