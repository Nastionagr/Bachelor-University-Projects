package program_code;
import java.io.Serializable;
import java.util.ArrayList;

public class Person implements Serializable {
    private boolean gender;
    private String name, country, address, email;

    public Person(boolean gender, String name, String country, String address, String email) {
        this.gender = gender;
        this.name = name;
        this.country = country;
        this.address = address;
        this.email = email;
    }
        
    public boolean getGender() {
        return gender;
    }

    public String getName() {
        return name;
    }

    public String getCountry() {
        return country;
    }

    public String getAddress() {
        return address;
    }

    public String getEmail() {
        return email;
    }

    public String toString(){
         String information = "";
         if (this.gender) {information += "Mrs. ";}
         else information += "Mr. ";
         return information + this.name + "(email:  " + this.email + ")";
    }
    
    public ArrayList<Reservation> seeAllReservations(ArrayList<Reservation> reservations)
    {
        ArrayList<Reservation> history = new ArrayList<Reservation>();
        
        for(Reservation r : reservations){
            if (this.equals(r.getPerson()))
                {history.add(r);}}
        
        return history;
    }
}