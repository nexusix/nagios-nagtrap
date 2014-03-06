//###########################################################################
//#
//# nagtrap.js -  NagTrap java functions
//#
//# Copyright (c) 2006 - 2007 Michael Luebben (nagtrap@nagtrap.org)
//# Last Modified: 13.10.2007
//#
//# License:
//#
//# This program is free software; you can redistribute it and/or modify
//# it under the terms of the GNU General Public License version 2 as
//# published by the Free Software Foundation.
//#
//# This program is distributed in the hope that it will be useful,
//# but WITHOUT ANY WARRANTY; without even the implied warranty of
//# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//# GNU General Public License for more details.
//#
//# You should have received a copy of the GNU General Public License
//# along with this program; if not, write to the Free Software
//# Foundation, Inc., 675 Mass Ave, Cambridge, MA 02139, USA.
//###########################################################################

function checkAll(obj) {
   var ss
   var kk
   kk= obj;

   ss=document.form1.checkbox.checked;

   if (ss == false) {
      kk ="no";
   }

   // set the form to look at (your form is called form1)
   var frm = document.form1
   
   // get the form elements
   var el = frm.elements
   
   // loop through the elements...
   for(i=0;i<el.length;i++) {
      // and check if it is a checkbox
      if(el[i].type == "checkbox" ) {
      // if it is a checkbox and you submitted yes to the function
      //alert(kk);
         if(kk == "yes")
            // tick the box
            el[i].checked = true;
         else
           // otherwise untick the box
           el[i].checked = false;
         }
   }
}
