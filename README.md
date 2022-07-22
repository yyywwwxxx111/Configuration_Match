# Configuration_Match

This script shows the result of matching one configuration file with another configuration file. The core algorithm of the project is the KMP string matching algorithm, which aims to match the name of each environment module and match the specific environment address, using string matching . The matching process is to first match the module name. If the module does not exist in the target file, the next module name is matched until a module with the same name is matched. After the module name is successfully matched, the specific environment address is matched. If successful, a prompt for successful matching is output.

## source1
<img width="194" alt="source1" src="https://user-images.githubusercontent.com/48043848/180377412-98f18555-f010-458c-a8db-bb9c86fe024c.png">

## source2
<img width="194" alt="source2" src="https://user-images.githubusercontent.com/48043848/180377463-9ea1406e-3f8f-4c18-8192-c24eeb4a5a40.png">

## result
<img width="243" alt="result" src="https://user-images.githubusercontent.com/48043848/180377476-89fb41a6-8b45-4de5-af1a-d250b1a9b3e3.png">
