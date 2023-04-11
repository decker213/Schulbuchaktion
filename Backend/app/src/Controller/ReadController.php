<?php

namespace App\Controller;

use App\Entity\Book;
use App\Entity\BookPrice;
use App\Entity\Publisher;
use App\Entity\Subject;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectRepository;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * https://symfonycasts.com/screencast/symfony-uploads/upload-request
 * https://symfonycasts.com/screencast/symfony-uploads/storing-uploaded-file#play
 */
class ReadController extends AbstractController
{
    #[Route('/read/xlsx/publisher', name: 'app_read_publisher')]
    public function readPublisher(Request $request, ManagerRegistry $registry, EntityManagerInterface $entityManager): Response
    {
        $repo = $registry->getRepository(Publisher::class);

        $this->deleteAllData($repo);

        if (file_exists("Schulbuchliste_4100_2023_2024.xlsx")) {
        $repoPublisher = $registry->getRepository(Publisher::class);
        $file = $request->files->get("schoolBookList");
        echo $file::class;
        if (file_exists($file->getClientOriginalName())) {
            $reader = IOFactory::createReader("Xlsx");
            $spreadsheet = $reader->load($file->getClientOriginalName());
            $entities = $repoPublisher->findAll();
            foreach ($entities as $entity) {
                $entityManager->remove($entity);
            }
            $entityManager->flush();
            $sheet = $spreadsheet->getSheet(0);
            for ($i = 2; $i <= $sheet->getHighestRow(); $i++) {
                $number = $sheet->getCell("J" . strval($i))->getValue();
                $name = $sheet->getCell("K" . strval($i))->getValue();

                $existing = $repoPublisher->findOneBy(["publisherNumber" => $number]);

                if (!isset($existing)) {
                    $publisher = new Publisher();
                    $publisher->setPublisherNumber($number);
                    $publisher->setName($name);
                    $repoPublisher->save($publisher, true);
                }
            }

        } else {
            die("file not found");
        }

        return $this->render('read/index.html.twig', [
            'controller_name' => 'ReadController',
        ]);
    }

    #[Route('/read/xlsx/subject', name: 'app_read_subject')]
    public function readSubject(ManagerRegistry $registry): Response
    {
        $repoSubject = $registry->getRepository(Subject::class);
        $repoUser = $registry->getRepository(User::class);

        $this->deleteAllData($repoSubject);

        if (file_exists("Schulbuchliste_4100_2023_2024.xlsx")) {
            $reader = IOFactory::createReader("Xlsx");
            $spreadsheet = $reader->load("Schulbuchliste_4100_2023_2024.xlsx");

            $sheet = $spreadsheet->getSheet(0);
            for ($i = 2; $i <= $sheet->getHighestRow(); $i++) {
                $user = "cchimani";
                $name = $sheet->getCell("F" . strval($i))->getValue();
                $shortName = "AM";

                $headOfSubjectId = $repoUser->findOneBy(["shortName" => $user]);
                $existing = $repoSubject->findOneBy(["name" => $name]);

                if (!isset($existing)) {
                    $subject = new Subject();
                    $subject->setHeadOfSubject($headOfSubjectId);
                    $subject->setName($name);
                    $subject->setShortName($shortName);
                    $repoSubject->save($subject, true);
                }
            }

        } else {
            die("file not found");
        }


        return $this->render('read/index.html.twig', [
            'controller_name' => 'ReadController',
        ]);
    }

    #[Route('/read/xlsx/book', name: 'app_read_book')]
    public function readBook(ManagerRegistry $registry): Response
    {
        $repoBook = $registry->getRepository(Book::class);
        $repoSubject = $registry->getRepository(Subject::class);
        $repoPublisher = $registry->getRepository(Publisher::class);

        $this->deleteAllData($repoBook);

        if (file_exists("Schulbuchliste_4100_2023_2024.xlsx")) {
            $reader = IOFactory::createReader("Xlsx");
            $spreadsheet = $reader->load("Schulbuchliste_4100_2023_2024.xlsx");

            $sheet = $spreadsheet->getSheet(0);
            for ($i = 2; $i <= $sheet->getHighestRow(); $i++) {
                $subject = $sheet->getCell("J" . strval($i))->getValue();
                $publisher = $sheet->getCell("K" . strval($i))->getValue();
                $mainBook = $sheet->getCell("L" . strval($i))->getValue();
                $bookNumber = $sheet->getCell("A" . strval($i))->getValue();
                $title = $sheet->getCell("C" . strval($i))->getValue();
                $shortTitle = $sheet->getCell("B" . strval($i))->getValue();
                $listType = $sheet->getCell("D" . strval($i))->getValue();
                $schoolForm = $sheet->getCell("E" . strval($i))->getValue();
                $info = $sheet->getCell("I" . strval($i))->getValue();
                $ebook = $sheet->getCell("P" . strval($i))->getValue();
                $ebookPlus = $sheet->getCell("Q" . strval($i))->getValue();

                $subjectId = $repoSubject->findOneBy(["name" => $subject]);
                $publisherId = $repoPublisher->findOneBy(["name" => $publisher]);
                $mainBookId = $repoBook->findOneBy(["id" => $mainBook]);

                $existing = $repoBook->findOneBy(["bookNumber" => $bookNumber]);

                if (!isset($existing)) {
                    $book = new Book();
                    $book->setSubject($subjectId);
                    $book->setPublisher($publisherId);
                    $book->setMainBook($mainBookId);
                    $book->setBookNumber($bookNumber);
                    $book->setTitle($title);
                    $book->setShortTitle($shortTitle);
                    $book->setListType($listType);
                    $book->setSchoolForm($schoolForm);
                    $book->setInfo($info);
                    $book->setEbook($ebook);
                    $book->setEbookPlus($ebookPlus);
                    $repoBook->save($book, true);
                }
            }

        } else {
            die("file not found");
        }


        return $this->render('read/index.html.twig', [
            'controller_name' => 'ReadController',
        ]);
    }

    #[Route('/read/xlsx/bookPrice', name: 'app_read_bookPrice')]
    public function readBookPrice(ManagerRegistry $registry): Response
    {
        $repoPublisher = $registry->getRepository(Publisher::class);
        $repoBook = $registry->getRepository(Book::class);
        $repoBookPrice = $registry->getRepository(BookPrice::class);

        $this->deleteAllData($repoBook);

        if (file_exists("Schulbuchliste_4100_2023_2024.xlsx")) {
            $reader = IOFactory::createReader("Xlsx");
            $spreadsheet = $reader->load("Schulbuchliste_4100_2023_2024.xlsx");

            $sheet = $spreadsheet->getSheet(0);
            for ($i = 2; $i <= $sheet->getHighestRow(); $i++) {
                $vnr = $sheet->getCell("M" . strval($i))->getValue();
                $book = $sheet->getCell("A" . strval($i))->getValue();
                $bookpricenormal = $sheet->getCell("N" . strval($i))->getValue();
                $bookpriceebook = $sheet->getCell("M" . strval($i))->getValue();
                $bookpriceplus = $sheet->getCell("O" . strval($i))->getValue();

                $existing = $repoPublisher->findOneBy(["publisherNumber" => $vnr]);
                $bookid = $repoBook->findOneBy(["bookNumber" => $book]);

                if (!isset($existing)) {
                    $bookprice = new BookPrice();
                    $bookprice->setBook($bookid);
                    $bookprice->setYear(date('Y'));
                    $bookprice->setPriceEbook($bookpriceebook);
                    $bookprice->setPriceEbookPlus($bookpriceplus);
                    $bookprice->setPriceInclusiveEbook($bookpricenormal);
                    $repoBookPrice->save($bookprice, true);
                }
            }
        } else {
            die("file not found");
        }


        return $this->render('read/index.html.twig', [
            'controller_name' => 'ReadController',
        ]);
    }

    public function deleteAllData(ObjectRepository $repo): Response
    {
        $myEntities = $repo->findAll();
        foreach ($myEntities as $myEntity) {
            $repo->remove($myEntity, true);
        }

        return $this->json(null, status: Response::HTTP_OK);
    }
}
