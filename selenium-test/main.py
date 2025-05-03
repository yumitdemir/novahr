from selenium import webdriver
from selenium.webdriver.edge.service import Service
from selenium.webdriver.common.by import By
from selenium.webdriver.edge.options import Options
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
from selenium.common.exceptions import NoSuchElementException, TimeoutException
import csv
import time

def setup_driver():
    """Set up and return the WebDriver."""
    edge_options = Options()
    # Enable headless mode
    # edge_options.add_argument("--headless")
    edge_options.add_argument("--window-size=1920,1080")
    driver = webdriver.Edge(options=edge_options)
    return driver

def login_and_logout():
    """Perform login and logout process and return status."""
    driver = setup_driver()
    try:
        # Navigate to the website
        print("Navigating to website...")
        driver.get("http://127.0.0.1:8000/")
        
        # Wait for the page to load and email field to be visible
        print("Waiting for login form...")
        wait = WebDriverWait(driver, 10)
        email_field = wait.until(EC.visibility_of_element_located((By.CSS_SELECTOR, "#data\\.email")))
        
        # Enter email
        print("Entering email...")
        email_field.send_keys("admin@gmail.com")
        
        # Enter password
        print("Entering password...")
        password_field = driver.find_element(By.CSS_SELECTOR, "#data\\.password")
        password_field.send_keys("admin@gmail.com")
        
        # Click login button
        print("Clicking login button...")
        login_button = driver.find_element(By.CSS_SELECTOR, ".fi-form-actions button")
        login_button.click()
        
        # Wait for dashboard to load - check for title containing Dashboard instead of URL
        print("Waiting for dashboard to load...")
        wait.until(EC.title_contains("Dashboard"))
        print(f"Current URL after login: {driver.current_url}")
        print(f"Page title: {driver.title}")
        
        # Find and click the sign out button using provided XPath
        print("Looking for sign out button...")
        signout_button = wait.until(EC.element_to_be_clickable((By.XPATH, "/html/body/div[1]/div[1]/main/div/section/div/div/div/div[1]/section/div/div/div/form/button[1]")))
        signout_button.click()
        print("Clicked sign out button")
        
        return "Login and logout successful"
    
    except (NoSuchElementException, TimeoutException) as e:
        return f"Error: {str(e)}"
    finally:
        driver.quit()

def create_department(driver, wait):
    """Navigate to departments and create a new department."""
    try:
        # Navigate to departments page
        print("Navigating to departments page...")
        driver.get("http://127.0.0.1:8000/departments")
        
        # Click on the "Add Department" button
        print("Clicking add department button...")
        add_button = wait.until(EC.element_to_be_clickable((By.XPATH, "/html/body/div[1]/div[1]/main/div/section/header/div[2]/div/a")))
        add_button.click()
        
        # Fill in department name
        print("Entering department name...")
        name_field = wait.until(EC.visibility_of_element_located((By.XPATH, "//*[@id=\"data.name\"]")))
        name_field.send_keys("departmenttest")
        
        # Fill in department description
        print("Entering department description...")
        desc_field = driver.find_element(By.XPATH, "//*[@id=\"data.description\"]")
        desc_field.send_keys("test department description test")
        
        # Click save button
        print("Clicking save button...")
        save_button = driver.find_element(By.XPATH, "//*[@id=\"key-bindings-1\"]")
        save_button.click()
        
        # Wait for redirect to edit page
        print("Waiting for redirect to edit page...")
        wait.until(EC.title_contains("Edit Department"))
        print(f"Current URL after save: {driver.current_url}")
        print(f"Page title: {driver.title}")
        
        return "Department created successfully"
        
    except (NoSuchElementException, TimeoutException) as e:
        print(f"Department creation error: {type(e).__name__}")
        print(f"Error occurred at: {driver.current_url}")
        try:
            print(f"Page title: {driver.title}")
            print(f"Page source excerpt: {driver.page_source[:500]}...")
        except:
            print("Could not get page details")
        return f"Department creation error: {str(e)}"

def test_navigation_links(driver, wait):
    """Test all sidebar navigation links to ensure they work correctly."""
    print("Testing sidebar navigation links...")
    results = []
    
    try:
        # First navigate to the dashboard to ensure sidebar is available
        driver.get("http://127.0.0.1:8000/")
        wait.until(EC.title_contains("Dashboard"))
        
        # Find all sidebar navigation links
        nav_links = driver.find_elements(By.CSS_SELECTOR, "a.fi-sidebar-item-button")
        print(f"Found {len(nav_links)} navigation links")
        
        # Store the href values before clicking (as clicking will change the DOM)
        link_data = []
        for link in nav_links:
            try:
                href = link.get_attribute("href")
                text = link.text.strip()
                if href and text:  # Only consider links with both href and text
                    link_data.append({"href": href, "text": text})
            except:
                continue
        
        # Now navigate to each link
        for data in link_data:
            try:
                print(f"Testing navigation to: {data['text']} ({data['href']})")
                driver.get(data['href'])
                
                # Wait for page to load (either by title or some common element)
                wait.until(EC.presence_of_element_located((By.TAG_NAME, "body")))
                time.sleep(1)  # Small delay to ensure page is fully loaded
                
                # Verify navigation succeeded
                current_url = driver.current_url
                page_title = driver.title
                
                print(f"  → Navigated to: {current_url}")
                print(f"  → Page title: {page_title}")
                
                # Check if we're on the expected page
                # This is a simple check - you might need more specific checks for each page
                if data['href'] in current_url or current_url in data['href']:
                    results.append(f"✓ Successfully navigated to {data['text']}")
                else:
                    results.append(f"✗ Failed to navigate to {data['text']} - Expected {data['href']}, got {current_url}")
            except Exception as e:
                results.append(f"✗ Error testing {data['text']}: {str(e)}")
        
        return results
    except Exception as e:
        return [f"Error testing navigation links: {str(e)}"]

def run_tests():
    """Run all test cases."""
    driver = setup_driver()
    try:
        wait = WebDriverWait(driver, 10)
        
        # Navigate to the website
        print("Navigating to website...")
        driver.get("http://127.0.0.1:8000/")
        
        # Login process
        print("Starting login process...")
        # Wait for login form and enter credentials
        email_field = wait.until(EC.visibility_of_element_located((By.CSS_SELECTOR, "#data\\.email")))
        email_field.send_keys("admin@gmail.com")
        password_field = driver.find_element(By.CSS_SELECTOR, "#data\\.password")
        password_field.send_keys("admin@gmail.com")
        login_button = driver.find_element(By.CSS_SELECTOR, ".fi-form-actions button")
        login_button.click()
        
        # Wait for dashboard to load
        wait.until(EC.title_contains("Dashboard"))
        print("Successfully logged in")
        
        # Test navigation links
        print("Testing navigation links...")
        nav_results = test_navigation_links(driver, wait)
        for result in nav_results:
            print(result)
        
        # Run department creation test
        dept_result = create_department(driver, wait)
        print(dept_result)
        
        # Navigate back to home page for logout
        print("Navigating back to home page...")
        driver.get("http://127.0.0.1:8000/")
        
        # Log out
        print("Logging out...")
        signout_button = wait.until(EC.element_to_be_clickable((By.XPATH, "/html/body/div[1]/div[1]/main/div/section/div/div/div/div[1]/section/div/div/div/form/button[1]")))
        signout_button.click()
        print("Logged out successfully")
        
        return "All tests completed successfully"
        
    except Exception as e:
        return f"Test error: {type(e).__name__} - {str(e)}"
    finally:
        driver.quit()

if __name__ == "__main__":
    result = run_tests()
    print(result)
