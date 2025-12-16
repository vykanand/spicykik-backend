// Initialize counters
let contador = 0;
let select_opt = 0;

function add_to_list(event) {
    // Prevent default form submission and multiple clicks
    if (event) {
        event.preventDefault();
        event.stopPropagation();
    }

    // Get form elements
    var actionSelect = document.querySelector('#action_select');
    var titleInput = document.getElementById('titleInput');
    var dateSelect = document.getElementById('date_select');
    
    // Basic validation
    if (!titleInput || !titleInput.value.trim()) {
        console.error('Title is required');
        if (window.parent && window.parent.notie) {
            window.parent.notie.alert({ type: 'error', text: 'Please enter a field name' });
        }
        return false;
    }
    
    // Disable add button to prevent multiple clicks
    var addButton = document.querySelector('.btn_add_fin[onclick*="add_to_list"]');
    if (addButton) {
        addButton.disabled = true;
        setTimeout(function() {
            addButton.disabled = false;
        }, 1000);
    }
    
    var action = actionSelect ? actionSelect.value : 'FIELD';
    var title = titleInput.value.trim();
    var date = dateSelect ? dateSelect.value : '';
 

switch (action) {
  case "FIELD":
 select_opt  = 0;
    break;
case "WORK":
select_opt = 1; 
    break;
}  
  
var class_li  =['list_shopping list_dsp_none','list_work list_dsp_none','list_sport list_dsp_none','list_music list_dsp_none'];  

// Create a unique ID for this item
    var currentId = contador++;
    
    // Create the HTML for the new item
    var cont = '<div class="col_md_1_list"><p>'+action+'</p></div>' +
               '<div class="col_md_2_list"><h4>'+title+'</h4></div>' +
               '<div class="col_md_3_list">' +
               '  <div class="cont_text_date"><p>'+date+'</p></div>' +
               '  <div class="cont_btns_options">' +
               '    <ul><li><a href="#" onclick="finish_action('+select_opt+','+currentId+', event); return false;">' +
               '    <i class="material-icons">delete</i></a></li></ul>' +
               '  </div>' +
               '</div>';
 
// Find the list container
    var listContainer = document.querySelector('.cont_princ_lists > ul');
    if (!listContainer) {
        console.error('Could not find the list container');
        return false;
    }
    
    // Create and append the new list item
    var li = document.createElement('li');
    li.className = class_li[select_opt] + ' li_num_' + currentId;
    li.setAttribute('draggable', true);
    li.innerHTML = cont;
    listContainer.appendChild(li);

// Animate the new item
    setTimeout(function() {  
        var newItem = document.querySelector('.li_num_'+currentId);
        if (newItem) {
            newItem.style.display = "block";
            
            // Add the animation class after a short delay
            setTimeout(function() {
                newItem.className = "list_dsp_true " + class_li[select_opt] + " li_num_" + currentId;
                
                // Re-enable the button after all animations are done
                var saveButton = document.querySelector('button[type="submit"], input[type="submit"]');
                if (saveButton) {
                    saveButton.disabled = false;
                }
            }, 50);
        }
    }, 100);

  // Clear the input field after adding
  if (titleInput) titleInput.value = '';
  // Focus back to the input field for better UX
  if (titleInput) titleInput.focus();
  
  // Re-enable the save button if it was disabled
  var saveButton = document.querySelector('.btn_add_fin[onclick*="aaq"]');
  if (saveButton) saveButton.disabled = false;
  
  return false;
}



function finish_action(num, num2, event) {
    try {
        if (event) {
            event.preventDefault();
            event.stopPropagation();
        }
        
        const class_li = ['list_shopping list_dsp_true', 'list_work list_dsp_true', 'list_sport list_dsp_true', 'list_music list_dsp_true'];
        const element = document.querySelector('.li_num_' + num2);
        
        if (!element) {
            console.error('Element not found:', '.li_num_' + num2);
            return;
        }
        
        element.className = class_li[num] + " list_finish_state";
        setTimeout(del_finish, 500);
    } catch (error) {
        console.error('Error in finish_action:', error);
        if (window.parent && window.parent.notie) {
            window.parent.notie.alert({
                type: 'error',
                text: 'An error occurred while processing your request',
                time: 3
            });
        }
    }
}

function del_finish() {
    try {
        const elements = document.querySelectorAll('.list_finish_state');
        
        // First pass: Apply styles for fade-out animation
        elements.forEach(element => {
            element.style.opacity = '0';
            element.style.height = '0';
            element.style.margin = '0';
            element.style.transition = 'all 0.3s ease';
        });

        // Second pass: Remove elements after animation completes
        setTimeout(() => {
            elements.forEach(element => {
                if (element && element.parentNode) {
                    element.parentNode.removeChild(element);
                }
            });
        }, 300);
    } catch (error) {
        console.error('Error in del_finish:', error);
    }
}
var t = 0;
function add_new(event){  
    if (event) {
        event.preventDefault();
        event.stopPropagation();
    }

    if(t % 2 == 0){  
        var crearNew = document.querySelector('.cont_crear_new');
        var tituloCont = document.querySelector('.cont_add_titulo_cont');
        if (crearNew && tituloCont) {
            crearNew.className = "cont_crear_new cont_crear_new_active";
            tituloCont.className = "cont_add_titulo_cont cont_add_titulo_cont_active";
            t++;
            // Focus the input when opening the form
            setTimeout(function() {
                var titleInput = document.getElementById('titleInput');
                if (titleInput) titleInput.focus();
            }, 100);
        }
    } else {  
        var crearNew = document.querySelector('.cont_crear_new');
        var tituloCont = document.querySelector('.cont_add_titulo_cont');
        if (crearNew && tituloCont) {
            crearNew.className = "cont_crear_new";
            tituloCont.className = "cont_add_titulo_cont";  
            t++;
        }
    }
    return false;
}




