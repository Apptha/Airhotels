
  /************************************************************\
  *
  *    Skinned Select Box Copyright 2007 Derek Harvey
  *		 www.lotsofcode.com
  *
  *    This file is part of Skinned Select Box .
  *
  *    Skinned Select Boxis free software; you can redistribute it and/or modify
  *    it under the terms of the GNU General Public License as published by
  *    the Free Software Foundation; either version 2 of the License, or
  *    (at your option) any later version.
  *
  *    Skinned Select Box is distributed in the hope that it will be useful,
  *    but WITHOUT ANY WARRANTY; without even the implied warranty of
  *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  *    GNU General Public License for more details.
  *
  *    You should have received a copy of the GNU General Public License
  *    along with Skinned Select Box; if not, write to the Free Software
  *    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
  *
  \************************************************************/
  
  // DEVELOPER NOTES
  // ===============
  
  // setText() Function for changing the text value of the selected item
  // This is reqired because the opacity for the select box
  // is set to "0", so we will not be able to see the value without this.
  
  // usage: setText(element [string], selectedOption [object])
  // example (applied to select box): 
  //  ->  setText('text1', this); - Uses current object as passing reference
  // example (onloaded): 
  //  ->  setText('text1', document.formName.elementName.selectedIndex); - Uses form element object as passing reference
  
  function setText(a, b)
  {
    x = document.getElementById(a);
    if (x)
      x.innerHTML = b.options[b.selectedIndex].innerHTML;
      
  };
