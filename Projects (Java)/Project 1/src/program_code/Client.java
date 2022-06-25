package program_code;

public class Client {
    private String name, country, city, address, postcode;

    public Client(String name, String country, String city, String address, String postcode) {
        this.name = name;
        this.country = country;
        this.city = city;
        this.address = address;
        this.postcode = postcode;
    }
 
    public String getName() {
        return name;
    }

    public void setName(String name) {
        this.name = name;
    }

    public String getCountry() {
        return country;
    }

    public void setCountry(String country) {
        this.country = country;
    }

    public String getCity() {
        return city;
    }

    public void setCity(String city) {
        this.city = city;
    }

    public String getAddress() {
        return address;
    }

    public void setAddress(String address) {
        this.address = address;
    }

    public String getPostcode() {
        return postcode;
    }

    public void setPostcode(String postcode) {
        this.postcode = postcode;
    }
    
    public String toString(){
        return this.name + " , " + this.country;
    }
}