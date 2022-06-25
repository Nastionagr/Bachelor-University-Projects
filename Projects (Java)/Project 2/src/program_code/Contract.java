package program_code;
import java.util.ArrayList;

public class Contract {
    private Employer employer;
    private ArrayList<Specialist> specialists;

    public Contract(Employer employer, ArrayList<Specialist> specialists) {
        this.employer = employer;
        this.specialists = specialists;
    }

    public Employer getEmployer() {
        return employer;
    }

    public void setEmployer(Employer employer) {
        this.employer = employer;
    }

    public ArrayList<Specialist> getSpecialists() {
        return specialists;
    }

    public void setSpecialists(ArrayList<Specialist> specialists) {
        this.specialists = specialists;
    }
    
    public String toString(){
        String names = "";
        int i = 0;
        for(Specialist s : specialists)
        {   if (i != 0){names += ", ";}
            names += s.getName(); i++;}
        return "EMPLOYER:   " + this.employer.getName() + "  -  EMPLOYEES:   " + names;
    }
}