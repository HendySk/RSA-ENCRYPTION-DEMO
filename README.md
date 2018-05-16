# RSA-ENCRYPTION
An RSA implementation with php gmp.
Hendy Soukaina.
Tangier.
Morocco.


In this project attempt was made to generate public and private keys and message signature (md5 Hash).

To test the project:
Step1: copy the project in your web directory.
Step2: open index html in your browser.
Step3: enter a bits number(2048 for example) in the text area and click OK.

the application should generate and display apublic and private keys and use them to encrypt and decrypt a dummy text
and display the result.

if you want to use the rsa.class  in your project do the following:

require("rsa.class");

$rsa= new rsa(2048/*for example*/);
    $rsa->generate();
    $msg="Lorem ipsum dolor Curabiturat dolor sollicitudin, tempus libero nec, tempor mauris. Nam finibus tempus arcu, in laoreet nisl tincidunt ac. Nam velit sapien, sagittis sit amet arcu vehicula, pulvinar vehicula justo. Mauris facilisis lacus quam, eget hendrerit lectus imperdiet vel. Morbi vulputate eros quam, eget scelerisque urna gravida eget. Curabitur imperdiet odio sit amet sagittis ornare. Donec semper quam vel posuere scelerisque. Fusce in efficitur justo. Aliquam blandit, augue scelerisque eleifend ornare, libero arcu interdum metus, vitae bibendum elit felis quis quam. Nulla sed elementum diam, non dictum massa. Donec dictum eleifend mi ac laoreet.";
    
    $rsa->t //returns Total execution time in seconds. (echo "Total execution time ".$rsa->t).
    $rsa->f //returns the Public Exponent.
    $rsa->n //returns the Modulus (also refered to as public key).
    $rsa->d //Private Exponent (also refered to as private key).
    $encryptedMessage=$rsa->enc($msg,"some password or signature here"); //choose you password and set it as second parameter.
    $decryptedMessage=$rsa->dec($encryptedMessage,"some password or signature here"); //use the same password you choosed above.
    
    
