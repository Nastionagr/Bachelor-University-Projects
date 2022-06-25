package gui;

import java.io.Serializable;
import java.util.ArrayList;
import program_code.Hotel;
import program_code.Person;
import program_code.Reservation;

public class Information implements Serializable {
    private ArrayList<Person> people;
    private ArrayList<Hotel> hotels;
    private ArrayList<Reservation> reservations;

    public Information(ArrayList<Person> people, ArrayList<Hotel> hotels, ArrayList<Reservation> reservations) {
        this.people = people;
        this.hotels = hotels;
        this.reservations = reservations;
    }

    public Information() {}

    public ArrayList<Person> getPeople() {
        return people;
    }

    public void setPeople(ArrayList<Person> people) {
        this.people = people;
    }

    public ArrayList<Hotel> getHotels() {
        return hotels;
    }

    public void setHotels(ArrayList<Hotel> hotels) {
        this.hotels = hotels;
    }

    public ArrayList<Reservation> getReservations() {
        return reservations;
    }

    public void setReservations(ArrayList<Reservation> reservations) {
        this.reservations = reservations;
    }
    
    
}
