from setrepcli import SetRepClient

def interactive_test_setrepclient_api():
    # Get user input for the required parameters
    BASE_URL = input("Please enter the base URL of the API: ").strip()
    USER_KEY = input("Please enter the user key: ").strip()
    USER_TOKEN = input("Please enter the user token: ").strip()
    APP_CODE = input("Please enter the app code: ").strip()
    
    # Creating an instance of the SetRepClient class
    client = SetRepClient(BASE_URL, USER_KEY, USER_TOKEN, APP_CODE)
    
    # Testing the get_sections method
    print("\nTesting get_sections method:")
    sections = client.get_sections()
    print("Sections:", sections)
    
    # If there are sections, test other methods using the first section
    if sections:
        section_code = sections[0]['code']
        
        # Testing the get_section_keys_values method
        print("\nTesting get_section_keys_values method for section:", section_code)
        keys_values = client.get_section_keys_values(section_code)
        print("Keys and Values:", keys_values)
        
        # If there are keys, test other methods using the first key
        if keys_values:
            key_code = keys_values[0]['code']
            
            # Testing the get_key_value method
            print("\nTesting get_key_value method for section:", section_code, "and key:", key_code)
            value = client.get_key_value(section_code, key_code)
            print("Value:", value)
            
            # Ask user if they want to set a new value for the key
            new_value = input(f"\nEnter a new value for key '{key_code}' or press Enter to skip: ").strip()
            if new_value:
                # Testing the set_key_value method
                print("\nTesting set_key_value method for section:", section_code, "and key:", key_code)
                success = client.set_key_value(section_code, key_code, new_value)
                print("Success:", success)
                
                # Re-fetching the value to verify the change
                updated_value = client.get_key_value(section_code, key_code)
                print("Updated Value:", updated_value)
    else:
        print("\nNo sections found. Skipping further tests.")

if __name__ == '__main__':
    interactive_test_setrepclient_api()
