/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */


var frmvalidator  = new Validator("post");
frmvalidator.addValidation("pzsp_short_name","alphanumeric","Short name must be alpha-numeric only - no spaces, dashes, underscores or other punctuation.");
frmvalidator.addValidation("pzsp_short_name","required","Short name is a required field.");
